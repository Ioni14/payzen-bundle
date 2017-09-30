Under active development
------------------------

Installation
============

Step 1: Download the Bundle
---------------------------

Using composer cli:

```console
$ composer require ioni/payzen-bundle "dev-master"
```

Step 2: Enable the Bundle
-------------------------

Enable the bundle in the AppKernel file:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Ioni\PayzenBundle\IoniPayzenBundle(),
        );
        // ...
    }
    // ...
}
```

TODO
====

* vads_action_mode : SILENT and IFRAME
* Payzen Web Services
* vads_page_action : REGISTER*, SUBSCRIBE...
* vads_url_check_src : BO, BATCH_AUTO, REC, MERCH_BO
* vads_operation_type : CREDIT
* vads_payment_config : MULTI
