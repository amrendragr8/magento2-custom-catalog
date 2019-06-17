How to install this extension?
Under your root folder, run the following command lines:

composer require amrendragr8/magento2-custom-catalog

php bin/magento setup:upgrade --keep-generated

php bin/magento setup:di:compile

php bin/magento cache:flush

How to see the results

Go to the backend

On the Magento Admin Panel, you navigate to the Catalog → Custom catalog
