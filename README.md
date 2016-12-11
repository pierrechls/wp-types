# Toolset Types

> The complete and reliable WordPress plugin for managing custom post types, custom taxonomies and custom fields.

Types let's you customize the WordPress admin by adding content types, custom fields and taxonomies. You will be able to craft the WordPress admin and turn it into your very own content management system.

## 🔮 How to use it

#### Download and activate

- **Clone** the project `git clone git@github.com:pierrechls/wp-types.git`
- **Upload** the *types* folder to the */wp-content/plugins/* directory
- **Activate** the plugin through the *Plugins* menu in WordPress

#### Create all your custom fields

Please follow the [user guides](https://wp-types.com/documentation/user-guides/).

#### Add your custom fields on your template

- Use shortcode in your WordPress post `[types field='your-field-slug'][/types]`

- Use PHP from your template fiels `echo do_shortcode( "[types field='your-field-slug'][/types]" )`

## 📚 Documentation

If you're an experienced PHP developer, you'll appreciate Types comprehensive [fields API](https://wp-types.com/documentation/customizing-sites-using-php/functions/).

### 