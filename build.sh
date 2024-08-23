#!/bin/bash

# Variables
container_name="build-container"
image_name="debian:latest"

rm -f EnderecoAMS-latest.zip, EnderecoShopware5Client-latest.zip

# Step 1: Create a tar archive excluding specific directories
tar --exclude='vendor' --exclude='node_modules' --exclude='.git' --exclude='shops' -czf workspace.tar.gz .

# Step 2: Start a Docker container with Debian or Ubuntu and install necessary tools
docker run -d --name $container_name -w /workspace $image_name tail -f /dev/null

# Step 3: Install rsync and zip in the Docker container
docker exec $container_name apt-get update
docker exec $container_name apt-get install -y rsync zip

# Step 4: Copy the tar archive to the container
docker cp workspace.tar.gz $container_name:/workspace/

# Step 5: Extract the tar archive inside the container
docker exec $container_name tar -xzf /workspace/workspace.tar.gz -C /workspace/
# Remove specific files and directories inside the Docker container
docker exec $container_name rm /workspace/workspace.tar.gz
docker exec $container_name rm -rf Mocks
docker exec $container_name rm -f .gitignore
docker exec $container_name rm -f .dockerignore
docker exec $container_name rm -f endereco.js
docker exec $container_name rm -f endereco.scss
docker exec $container_name rm -f package.json
docker exec $container_name rm -f package-lock.json
docker exec $container_name rm -f composer.json
docker exec $container_name rm -f composer-lock.json
docker exec $container_name rm -f webpack.config.js
docker exec $container_name rm -f docker-compose.yml
docker exec $container_name rm -f build-shopware5-client.sh
docker exec $container_name rm -f *.neon
docker exec $container_name rm -f *.sh


# Step 6: Execute the commands inside the Docker container

# Clean up old builds
docker exec $container_name rm -rf /workspace/EnderecoAMS /workspace/EnderecoShopware5Client

# Sync files and remove unnecessary ones for EnderecoAMS
docker exec $container_name rsync -ar --exclude='*.zip' /workspace/ /workspace/EnderecoAMS/
docker exec $container_name find EnderecoAMS -type f \
    -exec sed -i 's/EnderecoShopware5Client/EnderecoAMS/g' {} + \
    -exec sed -i 's/endereco_shopware5_client/endereco_ams/g' {} + \
    -exec sed -i 's/ (Download)//g' {} + \
    -exec sed -i 's/AGPLv3/proprietary/g' {} + \
    -exec sed -i 's/enderecoamsts/enderecoswamsts/g' {} + \
    -exec sed -i 's/enderecoamsstatus/enderecoswamsstatus/g' {} + \
    -exec sed -i 's/enderecoamsapredictions/enderecoswamsapredictions/g' {} + \
    -exec sed -i 's/enderecostreetname/enderecoswstreetname/g' {} + \
    -exec sed -i 's/enderecobuildingnumber/enderecoswbuildingnumber/g' {} + \
    -exec sed -i 's/setEnderecoamsts/setEnderecoswamsts/g' {} + \
    -exec sed -i 's/setEnderecoamsstatus/setEnderecoswamsstatus/g' {} + \
    -exec sed -i 's/setEnderecoamsapredictions/setEnderecoswamsapredictions/g' {} + \
    -exec sed -i 's/setEnderecostreetname/setEnderecoswstreetname/g' {} + \
    -exec sed -i 's/setEnderecobuildingnumber/setEnderecoswbuildingnumber/g' {} + \
    -exec sed -i 's/endereco_order_billingamsts/endereco_order_swbillingamsts/g' {} + \
    -exec sed -i 's/endereco_order_shippingamsts/endereco_order_swshippingamsts/g' {} + \
    -exec sed -i 's/endereco_order_billingamsstatus/endereco_order_swbillingamsstatus/g' {} + \
    -exec sed -i 's/endereco_order_shippingamsstatus/endereco_order_swshippingamsstatus/g' {} + \

docker exec $container_name find EnderecoAMS -type f -name '*EnderecoShopware5Client*' -exec sh -c '
    for file do
        new_name=$(echo "$file" | sed "s/EnderecoShopware5Client/EnderecoAMS/g")
        mv "$file" "$new_name"
    done
' sh {} +


docker exec $container_name zip -r ./EnderecoAMS-latest.zip ./EnderecoAMS
docker exec $container_name rm -rf /workspace/EnderecoAMS


# Sync files and remove unnecessary ones for EnderecoShopware5Client
docker exec $container_name rsync -ar --exclude='*.zip' /workspace/ /workspace/EnderecoShopware5Client/

docker exec $container_name zip -r ./EnderecoShopware5Client-latest.zip ./EnderecoShopware5Client
docker exec $container_name rm -rf /workspace/EnderecoShopware5Client

# Step 7: Download the created archives from the container to the host
docker cp $container_name:/workspace/EnderecoAMS-latest.zip ./
docker cp $container_name:/workspace/EnderecoShopware5Client-latest.zip ./

# Step 8: Clean up the Docker container
docker stop $container_name
docker rm $container_name
rm workspace.tar.gz

echo "Build completed and archives downloaded: EnderecoAMS-latest.zip, EnderecoShopware5Client-latest.zip"
