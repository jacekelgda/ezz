THANKS FOR PURCHASING TODDLERS!

Let's get started -

1.	What is a child theme??????
	https://codex.wordpress.org/Child_Themes

2.	How to use this child theme??????

If your changes are not simply CSS/Settings adjustments and you would like to make advanced edits to files in the theme, but preserve the ability to update the theme in the future without losing your changes, this is for you...

Copy the file you want to edit from the parent to the child theme folder and make your edits there...

Eg: To change the homepage template, copy page-home.php from
/wp-content/themes/toddlers/ 	into	/wp-content/themes/toddlers-child/
and make the edits on the child themes copy of the file.

If the file you want to edit sits in a subfolder of the theme, you must copy the file into the same folder structure on your child theme.

Eg: If you wanted to edit the authorinfo.php file, you would copy the file /wp-content/themes/toddlers/library/unf/authorinfo.php into

/wp-content/themes/toddlers-child/library/unf/authorinfo.php.

I have created some of the child themes subfolders for you, but you may need to add new folders if the file sits outside of the folders I've made for you.

3. USEFUL TIPS.

- Override CSS:

Style.css sits after compiled.css in the head. So if you make changes to the child themes style.css file, it will override compiled.css from the parent theme, in some cases CSS in the compiled file may be set to !important, just make sure you do the same in your own CSS to give it a higher priority.

- Override Images:

If you want to change one of the images in the theme, put the new image with the same name as the old image into the library/img folder. If the image is a background image, copy in the CSS declaring the background image into your child themes, style.css.

- Add New Functions:

Simply add the functions to the child themes functions.php and it will add the function on to the end of the parents functions.

- Override Parent Functions:

Handy if you need to add new menus or sidebars. Open the child themes function.php file and there will be an example of how to do this.