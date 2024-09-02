#!/bin/bash

# List of supported Shopware versions
declare -a versions=("5.3.0" "5.3.7" "5.4.6" "5.5.10" "5.6.10" "5.7.19")

# Function to check if an element is in the array
containsElement () {
  local e match="$1"
  shift
  for e; do [[ "$e" == "$match" ]] && return 0; done
  return 1
}

echo "Available Shopware 5 versions:"
printf " - %s\n" "${versions[@]}"

# Ask the user for the desired version
read -p "Enter the version of Shopware 5 you want to use: " version

# Check if the version is valid
if containsElement "$version" "${versions[@]}"; then
    echo "Preparing to start Shopware 5 in Dockware container with version $version"
    
    # Check and remove existing container if necessary
    if [ "$(docker ps -aq -f name=^shopware-$version$)" ]; then
        echo "Removing existing container named shopware-$version"
        docker rm -f shopware-$version
    fi

    # Start the Docker container
    docker run -d --name shopware-$version -v $(pwd):/var/www/html/custom/plugins/EnderecoShopware5Client -p 80:80 dockware/dev:$version

    sleep 10
    
    echo "Container started, Shopware 5 is available at http://localhost"
    echo "Your plugin is mounted at /var/www/html/custom/plugins/EnderecoShopware5Client"

    # Activate the plugin
    docker exec shopware-$version bash -c "cd /var/www/html && ./bin/console sw:plugin:refresh && ./bin/console sw:plugin:install --activate EnderecoShopware5Client && ./bin/console sw:cache:clear"

    echo "Plugin is activated."
else
    echo "Invalid version. Please enter a valid version from the list."
fi
