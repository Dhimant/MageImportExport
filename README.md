# MageImportExport
Magento2 Product Import Export Using CLI

##Inspired by the following article 
```bash
https://www.thirdandgrove.com/importing-products-magento2-part2
```
## Installation

Copy the folder Dhimant/ImpExp/ to your app/code

Run the following commands

```bash
php bin/magento module:enable Dhimant_ImpExp
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

## Usage

```bash
php bin/magento dhimant_impexp:importproducts /public_html/var/import/catalog_product.csv
```

## Help

```bash
php bin/magento dhimant_impexp:importproducts -h
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.
Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)