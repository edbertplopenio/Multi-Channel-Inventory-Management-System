<script>
    function initializeInventoryManagement() {
        let originalData = [];
        let lastCheckedProduct = null;
        let isVariantMode = false;
        let existingProductId = null;

        function capitalizeWords(str) {
            return str.replace(/\b\w/g, char => char.toUpperCase());
        }

        function refreshInventory() {
            fetch('../../backend/controllers/get_inventory.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateInventoryTables(data.items);
                    } else {
                        console.error('Error fetching inventory:', data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }


        function attachArchiveButtonListeners() {
            document.querySelectorAll('.action-button.archive').forEach(button => {
                button.addEventListener('click', function() {
                    const row = button.closest('tr');
                    const itemId = row.getAttribute('data-item-id');
                    const physicalQuantity = parseInt(row.querySelector('td:nth-child(5)').textContent) || 0;
                    const shopeeQuantity = parseInt(row.querySelector('td:nth-child(6)').textContent) || 0;
                    const tiktokQuantity = parseInt(row.querySelector('td:nth-child(7)').textContent) || 0;

                    // Check if all quantities are 0
                    const totalQuantity = physicalQuantity + shopeeQuantity + tiktokQuantity;

                    if (totalQuantity > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cannot Archive',
                            text: 'This item cannot be archived as it still has quantity in stock.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Proceed with the archive confirmation if total quantity is 0
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This item will be archived.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, archive it!',
                        cancelButtonText: 'No, keep it'
                    }).then(result => {
                        if (result.isConfirmed) {
                            // Send archive request to the server
                            fetch('../../backend/controllers/archive_item.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        item_id: itemId
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire('Archived!', data.message, 'success');
                                        refreshInventory(); // Refresh all tabs after archiving
                                    } else {
                                        Swal.fire('Error!', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire('Error!', 'Something went wrong while archiving the item.', 'error');
                                });
                        }
                    });
                });
            });
        }



        document.addEventListener('DOMContentLoaded', () => {
            function initializeSelectAllFeature() {
                const selectAllCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="select_all"]');
                const selectionBar = document.getElementById("selection-bar");
                const selectedCountDisplay = document.getElementById("selected-count");
                const archiveButton = selectionBar.querySelector('.selection-action.archive');

                function updateSelectionBar() {
                    const activeTabContent = document.querySelector('.tab-content.active');
                    const rowCheckboxes = activeTabContent.querySelectorAll('input[name="select_variant[]"]');
                    const selectedItems = activeTabContent.querySelectorAll('input[name="select_variant[]"]:checked');
                    const selectedCount = selectedItems.length;
                    selectedCountDisplay.textContent = `${selectedCount} items selected`;

                    if (selectedCount > 2) {
                        selectionBar.classList.remove('hidden');
                    } else {
                        selectionBar.classList.add('hidden');
                    }

                    const selectAllCheckbox = activeTabContent.querySelector('input[type="checkbox"][id^="select_all"]');
                    selectAllCheckbox.checked = selectedItems.length === rowCheckboxes.length && rowCheckboxes.length > 0;
                }

                archiveButton.addEventListener('click', function() {
                    const activeTabContent = document.querySelector('.tab-content.active');
                    const selectedItems = activeTabContent.querySelectorAll('input[name="select_variant[]"]:checked');
                    const dataType = activeTabContent.getAttribute('data-type');
                    const selectedIds = Array.from(selectedItems).map(item => item.value);

                    if (selectedIds.length === 0) return;

                    Swal.fire({
                        title: `Are you sure?`,
                        text: `You are about to archive ${selectedIds.length} items.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, archive them',
                        cancelButtonText: 'No, keep them'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            selectedIds.forEach(itemId => {
                                fetch('../../backend/controllers/archive_item.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            item_id: itemId,
                                            type: dataType
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        console.log(data);
                                        if (data.status === 'success') {
                                            const row = activeTabContent.querySelector(`tr[data-item-id="${itemId}"]`);
                                            if (row) row.remove();
                                        } else {
                                            console.error(`Failed to archive item with ID ${itemId}: ${data.message}`);
                                        }
                                    })
                                    .catch(error => {
                                        console.error(`Error archiving item with ID ${itemId}:`, error);
                                    });
                            });

                            selectionBar.classList.add('hidden');
                            const selectAllCheckbox = activeTabContent.querySelector('input[type="checkbox"][id^="select_all"]');
                            if (selectAllCheckbox) selectAllCheckbox.checked = false;
                        }
                    });
                });

                selectAllCheckboxes.forEach(selectAllCheckbox => {
                    selectAllCheckbox.addEventListener('change', function() {
                        const table = selectAllCheckbox.closest('.inventory-table');
                        const rowCheckboxes = table.querySelectorAll('input[name="select_variant[]"]');

                        rowCheckboxes.forEach(rowCheckbox => {
                            rowCheckbox.checked = selectAllCheckbox.checked;
                        });

                        updateSelectionBar();
                    });
                });

                document.querySelectorAll('.inventory-table input[name="select_variant[]"]').forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectionBar);
                });

                document.querySelector('.tabs-container').addEventListener('click', function(event) {
                    if (event.target.classList.contains('tab')) {
                        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));

                        event.target.classList.add('active');
                        const activeTabContent = document.getElementById(event.target.getAttribute('data-tab'));
                        activeTabContent.classList.add('active');

                        updateSelectionBar();
                    }
                });
            }

            initializeSelectAllFeature();
        });

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.action-button.archive').forEach(button => {
                button.addEventListener('click', function() {
                    const row = button.closest('tr');
                    const itemId = row.getAttribute('data-item-id');
                    const dataType = row.closest('.tab-content').getAttribute('data-type');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This will archive the item.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, archive it!',
                        cancelButtonText: 'No, keep it'
                    }).then(result => {
                        if (result.isConfirmed) {
                            fetch('../../backend/controllers/archive_item.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        item_id: itemId,
                                        type: dataType
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        row.remove();
                                        Swal.fire('Archived!', data.message, 'success');
                                    } else {
                                        Swal.fire('Error!', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire('Error!', 'Something went wrong while archiving the item.', 'error');
                                });
                        }
                    });
                });
            });
        });

        function fetchOriginalData() {
            console.log("Fetching original data...");
            fetch('../../backend/controllers/get_inventory.php')
                .then(response => response.json())
                .then(data => {
                    console.log("Fetched data:", data);
                    if (data.success) {
                        populateInventoryTables(data.items);
                    } else {
                        console.error('Error fetching inventory data:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error during fetchOriginalData:', error);
                });
        }

        function populateInventoryTables(items) {
            items.forEach(item => {
                const totalQuantity = item.quantity_physical_store + item.quantity_shopee + item.quantity_tiktok;
                const channelsText = item.channels.length === 3 ? 'All Channels' : item.channels.join(' and ');

                const allInventoryRow = `
                <tr data-item-id="${item.product_id}">
                    <td><input type="checkbox" name="select_variant[]" value="${item.product_id}"></td>
                    <td>${item.product_id}</td>
                    <td>${item.name}</td>
                    <td>${item.category}</td>
                    <td>${totalQuantity}</td>
                    <td>${item.size}</td>
                    <td>${item.color}</td>
                    <td>${item.price}</td>
                    <td>${item.date_added}</td>
                    <td>${channelsText}</td>
                    <td><img src="../../frontend/public/images/${item.image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                    <td>
                        <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                        <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                    </td>
                </tr>
            `;
                document.querySelector('#all-inventory .inventory-table tbody').insertAdjacentHTML('beforeend', allInventoryRow);

                if (item.quantity_physical_store > 0) {
                    const physicalStoreRow = `
                    <tr data-item-id="${item.product_id}">
                        <td><input type="checkbox" name="select_variant[]" value="${item.product_id}"></td>
                        <td>${item.product_id}</td>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.quantity_physical_store}</td>
                        <td>${item.size}</td>
                        <td>${item.color}</td>
                        <td>${item.price}</td>
                        <td>${item.date_added}</td>
                        <td><img src="../../frontend/public/images/${item.image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                        </td>
                    </tr>
                `;
                    document.querySelector('#physical-store .inventory-table tbody').insertAdjacentHTML('beforeend', physicalStoreRow);
                }

                if (item.quantity_shopee > 0) {
                    const shopeeRow = `
                    <tr data-item-id="${item.product_id}">
                        <td><input type="checkbox" name="select_variant[]" value="${item.product_id}"></td>
                        <td>${item.product_id}</td>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.quantity_shopee}</td>
                        <td>${item.size}</td>
                        <td>${item.color}</td>
                        <td>${item.price}</td>
                        <td>${item.date_added}</td>
                        <td><img src="../../frontend/public/images/${item.image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                        </td>
                    </tr>
                `;
                    document.querySelector('#shopee .inventory-table tbody').insertAdjacentHTML('beforeend', shopeeRow);
                }

                if (item.quantity_tiktok > 0) {
                    const tiktokRow = `
                    <tr data-item-id="${item.product_id}">
                        <td><input type="checkbox" name="select_variant[]" value="${item.product_id}"></td>
                        <td>${item.product_id}</td>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.quantity_tiktok}</td>
                        <td>${item.size}</td>
                        <td>${item.color}</td>
                        <td>${item.price}</td>
                        <td>${item.date_added}</td>
                        <td><img src="../../frontend/public/images/${item.image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                        </td>
                    </tr>
                `;
                    document.querySelector('#tiktok .inventory-table tbody').insertAdjacentHTML('beforeend', tiktokRow);
                }
            });

            attachArchiveButtonListeners();
        }

        document.querySelector('.tabs-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('tab')) {
                document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                event.target.classList.add('active');
                document.getElementById(event.target.getAttribute('data-tab')).classList.add('active');
            }
        });

        const modal = document.getElementById("new-item-modal");
        const newItemButton = document.querySelector(".new-item-button");
        const closeButton = document.querySelector(".close-button");

        newItemButton.addEventListener('click', function() {
            modal.style.display = "flex";
            resetFormFields();
            disableFormFields();
        });

        closeButton.addEventListener('click', closeModal);
        document.querySelector('.cancel-button').addEventListener('click', closeModal);

        function closeModal() {
            modal.style.display = "none";
            resetFormFields();
            disableFormFields();
        }

        document.querySelectorAll('.channel-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const quantityInput = this.closest('.channel-list').querySelector(`input[name="quantity-${this.value.toLowerCase().replace(' ', '-')}"]`);
                if (this.checked) {
                    quantityInput.removeAttribute('disabled');
                } else {
                    quantityInput.setAttribute('disabled', 'disabled');
                    quantityInput.value = "";
                }
            });
        });

        document.getElementById('apply-filters').addEventListener('click', function() {
            const selectedSize = document.getElementById('filter-size').value;
            const selectedColor = document.getElementById('filter-color').value;
            const selectedCategory = document.getElementById('filter-category').value;
            const selectedDate = document.getElementById('filter-date').value;
            const selectedChannel = document.getElementById('filter-channel').value;

            const rows = document.querySelectorAll('.inventory-table tbody tr');

            rows.forEach(row => {
                const size = row.querySelector('td:nth-child(5)').textContent;
                const color = row.querySelector('td:nth-child(6)').textContent;
                const category = row.querySelector('td:nth-child(3)').textContent;
                const dateAdded = row.querySelector('td:nth-child(8)').textContent;
                const channel = row.querySelector('td:nth-child(9)').textContent;

                let showRow = true;

                if (selectedSize && size !== selectedSize) {
                    showRow = false;
                }

                if (selectedColor && color !== selectedColor) {
                    showRow = false;
                }

                if (selectedCategory && category !== selectedCategory) {
                    showRow = false;
                }

                if (selectedDate && dateAdded !== selectedDate) {
                    showRow = false;
                }

                if (selectedChannel && channel !== selectedChannel) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        });

        document.getElementById('reset-filters').addEventListener('click', function() {
            document.getElementById('filter-size').value = "";
            document.getElementById('filter-color').value = "";
            document.getElementById('filter-category').value = "";
            document.getElementById('filter-date').value = "";
            document.getElementById('filter-channel').value = "";

            fetchOriginalData();
        });

        document.getElementById('new-item-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const productName = capitalizeWords(document.getElementById('name').value);
            const category = capitalizeWords(document.getElementById('category').value);
            const size = capitalizeWords(document.getElementById('size').value);
            const color = capitalizeWords(document.getElementById('color').value);

            fetch('../../backend/controllers/check_product_exists.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: productName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Product exists check response:", data);

                    if (data.exists) {
                        Swal.fire({
                            title: 'Product Exists',
                            text: 'Are you adding a variant of this product?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, it\'s a variant',
                            cancelButtonText: 'No, it\'s a new product'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                populateFormWithExistingProduct(data);
                                disableSpecificOptions(data.existing_sizes, data.existing_colors);
                                existingProductId = data.product_id;
                                isVariantMode = true;
                                console.log("Confirmed variant with existingProductId:", existingProductId);
                                submitForm(existingProductId, productName, category, size, color);
                            } else {
                                resetFormFields();
                                document.getElementById('name').value = "";
                                disableFormFields();
                            }
                        });
                    } else {
                        submitForm(null, productName, category, size, color);
                    }
                })
                .catch(error => {
                    console.error('Error checking product:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to check if product exists.',
                        confirmButtonText: 'OK'
                    });
                });
        });

        function submitForm(existingProductId, productName, category, size, color) {
            const formData = new FormData(document.getElementById('new-item-form'));

            const selectedChannels = Array.from(document.querySelectorAll('.channel-checkbox:checked'));

            console.log("Preparing to submit form data:", {
                existingProductId,
                productName,
                category,
                size,
                color,
                channels: selectedChannels.map(chk => chk.value)
            });

            if (selectedChannels.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select at least one channel and enter a quantity.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            let quantityProvided = true;

            selectedChannels.forEach(channel => {
                const quantityInput = document.querySelector(`input[name="quantity-${channel.value.toLowerCase().replace(' ', '-')}"]`);
                console.log(`Channel ${channel.value}, Quantity Provided: ${quantityInput.value}`);
                if (!quantityInput || quantityInput.value.trim() === "" || parseInt(quantityInput.value) <= 0) {
                    quantityProvided = false;
                }
            });

            if (!quantityProvided) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Each selected channel must have a valid quantity greater than zero.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            selectedChannels.forEach(channel => formData.append('channels[]', channel.value));
            formData.append('name', productName);
            formData.append('category', category);
            formData.append('size', size);
            formData.append('color', color);
            if (existingProductId) formData.append('existing_product_id', existingProductId);

            console.log("Final formData to submit:", Array.from(formData.entries()));

            fetch('../../backend/controllers/add_item.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Server response after form submission:', data);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Product updated successfully!',
                            confirmButtonText: 'OK'
                        });
                        document.getElementById('new-item-form').reset();
                        modal.style.display = "none";
                        refreshInventory();
                    } else {
                        console.error('Server returned error:', data.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: ' + data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Fetch error during form submission:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong! Please check the console for more details.',
                        confirmButtonText: 'OK'
                    });
                });
        }

        function resetFormFields() {
            const nameValue = document.getElementById('name').value;
            document.getElementById('new-item-form').reset();
            document.getElementById('name').value = nameValue;
            document.getElementById('category').removeAttribute('disabled');
            enableSizeAndColorFields();
            document.getElementById('name').focus();
            isVariantMode = false;
            existingProductId = null;
        }

        function enableSizeAndColorFields() {
            document.getElementById('size').removeAttribute('disabled');
            document.getElementById('color').removeAttribute('disabled');

            const sizeOptions = document.querySelectorAll('#size option');
            const colorOptions = document.querySelectorAll('#color option');

            sizeOptions.forEach(option => option.removeAttribute('disabled'));
            colorOptions.forEach(option => option.removeAttribute('disabled'));
        }

        function disableSpecificOptions(existingSizes, existingColors) {
            enableSizeAndColorFields();

            const sizeOptions = document.querySelectorAll('#size option');
            const colorOptions = document.querySelectorAll('#color option');

            sizeOptions.forEach(option => {
                if (existingSizes.includes(option.value)) {
                    option.setAttribute('disabled', 'disabled');
                }
            });

            colorOptions.forEach(option => {
                if (existingColors.includes(option.value)) {
                    option.setAttribute('disabled', 'disabled');
                }
            });
        }

        function disableFormFields() {
            const fieldsToDisable = ['category', 'size', 'color', 'price', 'date_added', 'image'];
            fieldsToDisable.forEach(field => {
                document.getElementById(field).setAttribute('disabled', 'disabled');
            });
        }

        function populateFormWithExistingProduct(product) {
            document.getElementById('category').value = product.category;
            document.getElementById('price').value = product.price;

            document.getElementById('category').setAttribute('disabled', 'disabled');
            document.getElementById('price').removeAttribute('disabled');
            document.getElementById('date_added').removeAttribute('disabled');
            document.getElementById('image').removeAttribute('disabled');
        }

        function handleProductNameInput() {
            const nameField = document.getElementById('name');

            nameField.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();

                    const productName = nameField.value.trim();

                    if (productName === lastCheckedProduct) return;

                    lastCheckedProduct = productName;

                    if (productName.length === 0) {
                        resetFormFields();
                        return;
                    }

                    if (isVariantMode) resetFormFields();

                    checkProductExists(productName);
                }
            });
        }

        function checkProductExists(productName) {
            console.log("Checking if product exists:", productName);

            fetch('../../backend/controllers/check_product_exists.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: productName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Product exists check response:", data);

                    if (data.exists) {
                        Swal.fire({
                            title: 'Product Exists',
                            text: 'Are you adding a variant of this product?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, it\'s a variant',
                            cancelButtonText: 'No, it\'s a new product'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                populateFormWithExistingProduct(data);
                                disableSpecificOptions(data.existing_sizes, data.existing_colors);
                                isVariantMode = true;
                                existingProductId = data.product_id;
                                console.log("Confirmed variant with existingProductId:", existingProductId);
                            } else {
                                resetFormFields();
                                document.getElementById('name').value = "";
                                disableFormFields();
                            }
                        });
                    } else {
                        enableAllFields();
                        isVariantMode = false;
                        existingProductId = null;
                    }
                })
                .catch(error => console.error('Error checking product:', error));
        }

        function enableAllFields() {
            const fieldsToEnable = ['category', 'size', 'color', 'price', 'date_added', 'image'];
            fieldsToEnable.forEach(field => {
                document.getElementById(field).removeAttribute('disabled');
            });
        }

        fetchOriginalData();
        disableFormFields();
        handleProductNameInput();
    }

    initializeInventoryManagement();
</script>