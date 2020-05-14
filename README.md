# Charitable Stat Shortcode
The `[charitable_stat]` shortcode is now available in Charitable core.

Installing this plugin will give you access to some new features, not currently available with Charitable.

## Usage
After installing and activating this plugin, you can use the `[charitable_stat]` shortcode.

For usage available with the existing integration in Charitable core, see [https://www.wpcharitable.com/documentation/stats-shortcode/](the documentation).

Below we outline the additional parameters available with this plugin.

**Show total raised in a category**
```
[charitable_stat category=my-category] // Refer to category by slug
```

**Show total raised in a tag**
```
[charitable_stat category=my-tag] // Refer to tag by slug
```

**Show total raised in a couple of categories**
```
[charitable_stat category=my-category,my-other-category] // Refer to tag by slug
```

**Default: Show total raised by a campaign and its child campaigns**
```
[charitable_stat campaigns=123]
```

**Default: Only show total raised by campaign itself (don't include child campaigns)**
```
[charitable_stat campaigns=123 include_children=0]
```

**Default: Show total raised by campaign itself (explictly include child campaigns)**
```
[charitable_stat campaigns=123 include_children=1]
```
