# Sane HTML Newsletters (working title)

## Description

Convert any page generated by WordPress into HTML with inline styles to be used in newsletters.

## Installation

1. Clone the repo
1. run ```composer install``` from freshly cloned plugin root
1. Activate the plugin
1. Add a filter with path to your newsletter css (defaults to current theme's style.css)
1. Create newsletter template (see e.g. below)
1. Enjoy your sanity while dealing with HTML newsletters

## Example

All you need to do is to start your template file with ```ob_start();``` and end it with ```echo Sane_HTML_Newsletters::convert( ob_get_clean() );```

```php
ob_start();

// Regular WP template goes here

echo Sane_HTML_Newsletters::convert( ob_get_clean() );
```

Alternatively, you can use ```shnl_should_auto_convert``` filter to determine whether current request's output should be autoconverted

```php
add_filter( 'shnl_should_auto_convert', function( $should_convert ) {
	if ( is_page( 'my-newsletter-page' ) )
		return true;

	return $should_convert;
} );
```

Use a filter to specify path to desired CSS file:
```php
add_filter( 'shnl_stylesheet_path', function( $css_file_path ) { return 'my/absolute/path/to/newsletter.css' } );
```

## Todo


## Developers

Miss a feature? Pull requests are welcome.

## Props

Tijs Verkoyen for his [CssToInlineStyles](https://github.com/tijsverkoyen/CssToInlineStyles) class.