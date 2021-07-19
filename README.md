# acf-jsons-loader

Loader for acf .json files that can be included with a theme or plugin   

to include in your project run:

```composer require hadamlenz/acf-jsons-loader```

to use the class add this somewhere in your code:

```new Acf_Jsons_loader( $root , $json );```

**$root** is the root of the project, the theme or plugins folder
**$json** is a path or array of paths to ACF Json files that define field groups.  if usiing an array, it should be an associative array with 'group_id' => 'path to the file'

## Example

```
$theme_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
new Acf_Jsons_loader( $theme_dir , '/assets/all-fields.json' );
```
or

```
new Acf_Jsons_loader( $theme_dir , array(
'group_abcde123456789' => 'my-group-1.json',
'group_987654321edcba' => 'my-group-2.json',
));
```