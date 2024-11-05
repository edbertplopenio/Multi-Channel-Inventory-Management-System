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
        document.addEventListener('click', function(event) {
            const button = event.target.closest('.action-button.archive');
            if (!button) return;

            console.log('Archive button clicked'); // Debug log
            const row = button.closest('tr');
            if (!row) {
                console.error('No row found for archive action');
                return;
            }

            const itemId = row.getAttribute('data-item-id');
            if (!itemId) {
                console.error('Item ID not found');
                return;
            }

            const physicalQuantity = parseInt(row.querySelector('td:nth-child(5)').textContent) || 0;
            const shopeeQuantity = parseInt(row.querySelector('td:nth-child(6)').textContent) || 0;
            const tiktokQuantity = parseInt(row.querySelector('td:nth-child(7)').textContent) || 0;

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

            Swal.fire({
                title: 'Are you sure?',
                text: 'This item will be archived.',
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
                        body: JSON.stringify({ item_id: itemId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                row.remove();
                                Swal.fire('Archived!', data.message, 'success');
                                refreshInventory();
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
    }

    function initializeSelectAllFeature() {
        document.addEventListener('change', function(event) {
            if (event.target.matches('input[type="checkbox"][id^="select_all"]')) {
                const selectAllCheckbox = event.target;
                const table = selectAllCheckbox.closest('.inventory-table');
                const rowCheckboxes = table.querySelectorAll('input[name="select_variant[]"]');

                rowCheckboxes.forEach(rowCheckbox => {
                    rowCheckbox.checked = selectAllCheckbox.checked;
                });

                updateSelectionBar();
            }
        });

        document.addEventListener('change', function(event) {
            if (event.target.matches('.inventory-table input[name="select_variant[]"]')) {
                updateSelectionBar();
            }
        });
    }

    function updateSelectionBar() {
        const activeTabContent = document.querySelector('.tab-content.active');
        const rowCheckboxes = activeTabContent.querySelectorAll('input[name="select_variant[]"]');
        const selectedItems = activeTabContent.querySelectorAll('input[name="select_variant[]"]:checked');
        const selectedCount = selectedItems.length;
        const selectionBar = document.getElementById("selection-bar");
        const selectedCountDisplay = document.getElementById("selected-count");

        selectedCountDisplay.textContent = `${selectedCount} items selected`;

        if (selectedCount > 0) {
            selectionBar.classList.remove('hidden');
        } else {
            selectionBar.classList.add('hidden');
        }

        const selectAllCheckbox = activeTabContent.querySelector('input[type="checkbox"][id^="select_all"]');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = selectedItems.length === rowCheckboxes.length && rowCheckboxes.length > 0;
        }
    }

    function initializeTabClickListener() {
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

    function initializeArchiveButton() {
        document.addEventListener('click', function(event) {
            const archiveButton = event.target.closest('.selection-bar .action-button.archive');
            if (!archiveButton) return;

            const activeTabContent = document.querySelector('.tab-content.active');
            const selectedItems = activeTabContent.querySelectorAll('input[name="select_variant[]"]:checked');
            const selectedIds = Array.from(selectedItems).map(item => item.value);

            if (selectedIds.length === 0) return;

            let itemsToArchive = [];
            let itemsWithQuantity = [];

            selectedIds.forEach(itemId => {
                const rowsAcrossTables = document.querySelectorAll(`tr[data-item-id="${itemId}"]`);
                let totalQuantity = 0;

                rowsAcrossTables.forEach(row => {
                    const quantityCell = row.querySelector('td:nth-child(5)');
                    const quantity = parseInt(quantityCell ? quantityCell.textContent : '0') || 0;
                    totalQuantity += quantity;
                });

                if (totalQuantity > 0) {
                    itemsWithQuantity.push(itemId);
                } else {
                    itemsToArchive.push(itemId);
                }
            });

            if (itemsWithQuantity.length > 0 && itemsToArchive.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Mixed Selection',
                    text: 'Some selected items cannot be archived as they still have quantity in stock. Only items with zero quantity will be archived.',
                    confirmButtonText: 'Proceed'
                }).then(result => {
                    if (result.isConfirmed) {
                        archiveItems(itemsToArchive);
                    }
                });
            } else if (itemsWithQuantity.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Archive',
                    text: 'All selected items have quantity in stock and cannot be archived.',
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    title: `Are you sure?`,
                    text: `You are about to archive ${itemsToArchive.length} items.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, archive them',
                    cancelButtonText: 'No, keep them'
                }).then(result => {
                    if (result.isConfirmed) {
                        archiveItems(itemsToArchive);
                    }
                });
            }
        });

        function archiveItems(itemIds) {
            itemIds.forEach(itemId => {
                const rowsAcrossTables = document.querySelectorAll(`tr[data-item-id="${itemId}"]`);

                fetch('../../backend/controllers/archive_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ item_id: itemId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            rowsAcrossTables.forEach(row => row.remove());
                            Swal.fire('Archived!', `Item ID ${itemId} has been archived successfully.`, 'success');
                        } else {
                            Swal.fire('Error!', `Failed to archive item ID ${itemId}: ${data.message}`, 'error');
                        }
                    })
                    .catch(error => {
                        console.error(`Error archiving item with ID ${itemId}:`, error);
                        Swal.fire('Error!', 'Something went wrong while archiving the item.', 'error');
                    });
            });

            const selectionBar = document.getElementById("selection-bar");
            selectionBar.classList.add('hidden');
            const selectAllCheckbox = document.querySelector('.tab-content.active input[type="checkbox"][id^="select_all"]');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }
    }

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

        document.querySelectorAll('#size option').forEach(option => option.removeAttribute('disabled'));
        document.querySelectorAll('#color option').forEach(option => option.removeAttribute('disabled'));
    }

    function disableSpecificOptions(existingSizes, existingColors) {
        enableSizeAndColorFields();

        document.querySelectorAll('#size option').forEach(option => {
            if (existingSizes.includes(option.value)) {
                option.setAttribute('disabled', 'disabled');
            }
        });

        document.querySelectorAll('#color option').forEach(option => {
            if (existingColors.includes(option.value)) {
                option.setAttribute('disabled', 'disabled');
            }
        });
    }

    function disableFormFields() {
        ['category', 'size', 'color', 'price', 'date_added', 'image'].forEach(field => {
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
            body: JSON.stringify({ name: productName })
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
                    }).then(result => {
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
        ['category', 'size', 'color', 'price', 'date_added', 'image'].forEach(field => {
            document.getElementById(field).removeAttribute('disabled');
        });
    }

    fetchOriginalData();
    disableFormFields();
    handleProductNameInput();
}

if (document.querySelector('.inventory-container')) {
    initializeInventoryManagement();
}
