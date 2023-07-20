# FFXIV Crafting
## Crafting As A Service
### An online tool to help crafters in Final Fantasy XIV: A Realm Reborn.

## Routine Updates

Image for Footer: http://na.finalfantasyxiv.com/lodestone/special/patchnote_log/

## Updating

These commands should be done in Vagrant.

```
php artisan cache:clear file (optionally necessary)
php artisan aspir:data
php artisan aspir:migrate
php artisan aspir:build-db
php artisan aspir:assets
```

These commands should be done on the Mac.

```
dep cactuar:db
dep cactuar:assets
dep deploy
```
