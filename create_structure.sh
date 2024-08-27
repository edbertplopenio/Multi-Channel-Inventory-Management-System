# Create backend directories and files
mkdir -p backend/{controllers,models,routes,services,config,utils}
touch backend/{app.js,server.js,package.json}

# Create frontend directories and files
mkdir -p frontend/{public/{styles,scripts},src/{components,pages,services}}
touch frontend/public/{index.html,styles/{main.css,reset.css},scripts/{main.js,utils.js}}
touch frontend/src/{components/{Header.js,Footer.js,InventoryItem.js},pages/{Home.js,Products.js,Contact.js},services/api.js,App.js}
touch frontend/package.json

# Create database directories and files
mkdir -p database/{migrations,seeders,models}
touch database/config.js

# Create tests directories
mkdir -p tests/{backend,frontend,integration}

# Create docs directory and files
mkdir docs
touch docs/{requirements.md,design.md,api-specification.md}

# Create deployment directories and files
mkdir deployment
touch deployment/{Dockerfile,docker-compose.yml,nginx.conf,deploy.sh,hostinger-config.md}

# Create environment file and README
touch .gitignore .env README.md

echo "Project structure created successfully."
