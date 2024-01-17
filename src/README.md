## Deployment

**Set up below continues executing task(s) in background**

- php artisan queue:work --queue=reset-password-email
- php artisan queue:work --queue=import-users
- php artisan queue:work --queue=notification-email
- php artisan queue:work --queue=import-course-participant
- php artisan queue:work --queue=export-individual-performance-report
- php artisan queue:work --queue=export-course-performance-report

**Set up below cron job(s)**

- <p>* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1</p>

**Web server settings**

Increase max upload size so file uploader can upload large files

**PHP**

`post_max_size=1024M`

`upload_max_filesize=1024M`

**Nginx**

`client_max_body_size=1024M`

## Development

### File Chooser
We are using [laravel-file-manager](https://github.com/alexusmai/laravel-file-manager "Github")

To open file chooser
 
```
$('#button-image').click(function(){
    var win = window.open('/file-manager/fm-button', 'fm', 'width=800,height=600');
 });
```

To get the selected file we can add js function as callback 

```
function fmSetLink($url) {
    var prefix = "{{config('app.oss_root_path')}}";
    var url = $url.substr($url.indexOf(prefix) + prefix.length, $url.length);
    document.getElementById('image_label').value = url;
}
```

`Note: function name should not be change`

To configure file chooser with CKEditor

```
CKEDITOR.replace('editor1', {filebrowserImageBrowseUrl: '/file-manager/ckeditor'});
```

Note: More documentation is mentioned on file manager [git repository](https://github.com/alexusmai/laravel-file-manager/tree/master/docs)
