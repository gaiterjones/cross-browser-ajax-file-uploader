## Cross Browser Ajax File Uploader - gaiterjones.com

Live demo at [http://blog.gaiterjones.com/cross-browser-ajax-php-file-uploader/](http://)

### Synopsis
A simple PHP ajax file uploader using XHR (HTML5) and Flash (Uploadify). Can be used as a plugin to provide cross browser file upload functionality.

### Version
***
	@version		1.0.0
	@since			04 2013
	@author			gaiterjones
	@documentation	blog.gaiterjones.com
	@twitter		twitter.com/gaiterjones
	
### Installation

Copy the files to your www folder.

### Configuration

Edit the filea

	config/applicationConfig.php
	lib/js/fileUpload.js
	

Add the path to your upload folder - must be writeable. Add a comma seperated list of supported file types in the applicationConfig file.

Update the path to fileupload.php in the two ajax function in fileUpload.js to match the installation path of the code. You may also need to update the path to the uploadify swf too.

Look at the code in demo.html and copy the XHR and Flash divs as well as the required javascript and css files to your own html.

The image resize library is included to allow you to resize images as they are uploaded. Specify the image size in the ajax get request, e.g. uploadresize=600x600.


## Acknowledgements
http://www.uploadify.com/ for the Flash uploader.
Jarrod Oberto for the image library.

## License

The MIT License (MIT)
Copyright (c) 2013 Peter Jones

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.