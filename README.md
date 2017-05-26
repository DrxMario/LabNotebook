# LabNotebook
A simple front end to help manage an online lab notebook.

To install on your webserver, simply copy all files and folders to the appropriate directory, 
Then replace the code in upload.php and process.php that says
```
header('location:http://PUT/YOUR/LANDING/PAGE/URL/HERE');
```
with the URL of the landing page for your notebook.

Directories contained in the root directory where this notebook is installed will appear in the header of the landing page, allowing for users to easily navigate across files and directories from within their browser.

WARNING: This code is provided with NO WARRANTY, and provides functionality that allows users to upload files to your webserver. This code DOES NOT claim to provide any protection against malicious file uploading. Be sure you know what you're doing before you install this website on your server. If you find this code useful, consider limiting who can upload or modify files using [htaccess control](http://www.htaccess-guide.com/). The code in index.php contains examples of how to check for a specific user, eg:
```
$auth_username = $_SERVER["REMOTE_USER"];
// To restrict user access, change usernames appropriately:
if (($auth_username != "user1")&&($auth_username != "user2")){ /* do something restricted here */ }
```

This website relies on the [PHP Excel Spreadsheet reader](http://pear.php.net/package/Spreadsheet_Excel_Reader) (distributed under the PHP license) and the [CKEditor](http://ckeditor.com/) (distributed under the GPL license).
