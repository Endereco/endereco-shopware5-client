#!/bin/bash

branch=$(git symbolic-ref HEAD | sed -e 's,.*/\(.*\),\1,')

rm -rf EnderecoAMS
rsync -avr ./* ./EnderecoAMS
rm -rf EnderecoAMS/Mocks
rm -rf EnderecoAMS/shops
rm -rf EnderecoAMS/node_modules
rm -rf EnderecoAMS/vendor
rm -f EnderecoAMS/.gitignore
rm -f EnderecoAMS/.dockerignore
rm -f EnderecoAMS/endereco.js
rm -f EnderecoAMS/endereco.scss
rm -f EnderecoAMS/package.json
rm -f EnderecoAMS/package-lock.json
rm -f EnderecoAMS/composer.json
rm -f EnderecoAMS/composer-lock.json
rm -f EnderecoAMS/webpack.config.js
rm -f EnderecoAMS/docker-compose.yml
rm -f EnderecoAMS/build-shopware5-client.sh
rm -f EnderecoAMS/*.neon
find ./EnderecoAMS -type f -exec sed -i 's/EnderecoShopware5Client/EnderecoAMS/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/endereco_shopware5_client/endereco_ams/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/ (Download)//g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/AGPLv3/proprietary/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/enderecoamsts/enderecoswamsts/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/enderecoamsstatus/enderecoswamsstatus/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/enderecoamsapredictions/enderecoswamsapredictions/g' {} \;

find ./EnderecoAMS -type f -exec sed -i 's/enderecostreetname/enderecoswstreetname/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/enderecobuildingnumber/enderecoswbuildingnumber/g' {} \;

find ./EnderecoAMS -type f -exec sed -i 's/setEnderecoamsts/setEnderecoswamsts/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/setEnderecoamsstatus/setEnderecoswamsstatus/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/setEnderecoamsapredictions/setEnderecoswamsapredictions/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/setEnderecostreetname/setEnderecoswstreetname/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/setEnderecobuildingnumber/setEnderecoswbuildingnumber/g' {} \;

find ./EnderecoAMS -type f -exec sed -i 's/endereco_order_billingamsts/endereco_order_swbillingamsts/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/endereco_order_shippingamsts/endereco_order_swshippingamsts/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/endereco_order_billingamsstatus/endereco_order_swbillingamsstatus/g' {} \;
find ./EnderecoAMS -type f -exec sed -i 's/endereco_order_shippingamsstatus/endereco_order_swshippingamsstatus/g' {} \;

find ./EnderecoAMS -type f -exec rename 's/EnderecoShopware5Client/EnderecoAMS/g' {} \;
zip -r ../EnderecoAMS-$branch.zip EnderecoAMS
rm -rf EnderecoAMS


rm -rf EnderecoShopware5Client
rsync -avr ./* ./EnderecoShopware5Client
rm -rf EnderecoShopware5Client/Mocks
rm -rf EnderecoShopware5Client/shops
rm -rf EnderecoShopware5Client/node_modules
rm -rf EnderecoShopware5Client/vendor
rm -f EnderecoShopware5Client/.gitignore
rm -f EnderecoShopware5Client/.dockerignore
rm -f EnderecoShopware5Client/endereco.js
rm -f EnderecoShopware5Client/endereco.scss
rm -f EnderecoShopware5Client/package.json
rm -f EnderecoShopware5Client/package-lock.json
rm -f EnderecoShopware5Client/composer.json
rm -f EnderecoShopware5Client/composer-lock.json
rm -f EnderecoShopware5Client/webpack.config.js
rm -f EnderecoShopware5Client/docker-compose.yml
rm -f EnderecoShopware5Client/build-shopware5-client.sh
rm -f EnderecoShopware5Client/*.neon

zip -r ../EnderecoShopware5Client-$branch.zip EnderecoShopware5Client
rm -rf EnderecoShopware5Client