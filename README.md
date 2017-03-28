YobiApps
=========

> WARNING: YobiApps are intended for experimenting and learning, NOT for a production environment.

![Image of Yobi](http://www.primechain.in/img/github_yobiapps.png)

YobiApps are a suite of apps built in php that can be readily deployed on [Multichain](https://github.com/MultiChain). The YobiApps project is maintained by [Primechain Technologies Pvt. Ltd.](http://www.primechain.in).

System Requirements
-------------------

An instance of [YobiChain](https://github.com/Primechain/yobichain)

Installation
------------

YobiApps are automatically installed during the [YobiChain](https://github.com/Primechain/yobichain) setup.

Creating an account
---------------------
* Visit `http://<IP Address>/yobiapps/register.php` and enter your name, username and password.
* Your account will be created.
* You will automatially be taken to the sigin page at `http://<IP Address>/yobiapps/login.php`
* Enter your username and password to login.


PrimeVault
------------

1. PrimeVault is a simple blockchain powered document storage and retrieval system.

2. To access PrimeVault visit `http://<IP Address>/yobiapps/vault_upload.php`

3. Select a file to upload, add a description and click upload.

4. If all goes well, you should see a success mesage and the transaction id.

5. Clicking on the transaction id will take you to the Transaction Details page which shows:

  + the Transaction Id - can be clicked to view details
  + Uploader  - can be clicked to view details
  + Block Hash - can be clicked to view details
  + Confirmations
  + Date of Upload 
  + Description 
  + Download Link

6. To view / search uploads, visit `http://<IP Address>/yobiapps/vault_download.php`

PrimeContract
------------

1. PrimeContract is a simple blockchain powered system for digitally signng contracts.

2. To upload a contract, visit `http://<IP Address>/yobiapps/contract_upload.php`

3. To invite signees, visit `http://<IP Address>/yobiapps/contract_invite.php`

4. To view contracts, visit `http://<IP Address>/yobiapps/contracts_history.php`

5. To sign a contract for which you hve been invited, visit `http://<IP Address>/yobiapps/contract_sign.php`

6. To view details of a particular contract, visit `http://<IP Address>/yobiapps/contract_view_details.php`


YobiWallet
------------

1. YobiWallet is a simple blockchain powered wallet for Yobicoins, a smart asset.

2. To send Yobicoins, visit `http://<IP Address>/yobiapps/ic_send_money.php`

3. To view your transactions, visit `http://<IP Address>/yobiapps/ic_view_history.php`

Live demo
---------
Links to the live demo can be found here - [https://github.com/Primechain/yobichain](https://github.com/Primechain/yobichain).

Planned roadmap
-----
- [ ] Secure messaging server


Contributors
-------------
A non-exhaustive list of contributors:
* Sripathi Srinivasan (sripathi@primechain.in) [Project Lead]
* Rohas Nagpal (rohas@primechain.in)
* Sudin Baraokar (HEAD.SBICIC@sbi.co.in)
* Shinam Arora (shinam@primechain.in)
