# Afterlogic doc viewer

Self-hosted Collabora Code based document viewer for Afterlogic WebMail solutions, replacing 3rd party Microsoft/Google view. 

### Why?

Because I wanted a handy document viewer for my mails' attachments in Afterlogic and I refuse to use Microsoft's or Google's cloud based solutions for my personal documents. I also had a Collabora Code server running for online office purposes in my Nextcloud instance. Why not combine the two?

### Requirements

* Working Afterlogic WebMail installation, preferrably using Nginx webserver with PHP-FPM *(Apache or anything else works too but you'll have to work out how aliasing works)*
* Collabora CODE server running *(You should totally use it for your Nextcloud too)*

### Setup

1. Copy files

   Create an arbitrary directory inside Afterlogic's root directory. This example uses `office`. If your Afterlogic files are at `/afterlogic`, it should be `/afterlogic/office`. Copy the php files ( `config.php`, `index.php`, `endpoints.php` ) to the frehsly created directory.


1. Nginx *(Webserver)* setup

   The provided `nginx.conf` is a working example. Yours should look similar. The only relevant addition is the following:

   ```
   location ~ /office/wopi/ {
      alias /afterlogic/office/wopi/;
      try_files $uri $uri/ /office/endpoints.php?$query_string;
   }
   ```

   You should replace `/office` and `/afterlogic` with your own paths. `/afterlogic` is the path of Afterlogic's files' root, `/office` is orbitrary but need to match with other config files. In this example Afterlogic is accessible on `mail.domain.com` and the doc view is at `mail.domain.com/office`. It doesn't matter all that much, won't have to call it manually ever.
2. Doc viewer configuration

   Add your own servers/directories to `config.php`

   Note: `$MAIL_SERVER_OFFICE_DIRECTORY` needs to match with Nginx config's `/office`!
3. Afterlogic configuration

   Configure Afterlogic as described on their page: <https://afterlogic.com/docs/webmail-pro/frequently-asked-questions/office-document-viewer>

   Example config (`OfficeDocumentViewer.config.json`) supplied, just edit the `ViewerUrl` parameter and copy to `data/settings/modules/OfficeDocumentViewer.config.json`

   Note: The `/office` suffix/directory needs to match with nginx & doc viewer configurations!

### Disclaimer

I am no PHP developer and I hacked this together with zero deeper knowledge regarding Collabora. Please use at your own risk.

### Links

Afterlogic - <https://afterlogic.org/webmail-lite>

Collabora CODE - <https://www.collaboraoffice.com/code/>
