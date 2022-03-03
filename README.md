# Charitable Stat Shortcode
The `[charitable_stat]` shortcode is now available in Charitable core.

Installing this plugin will give you access to some new features, not currently available with Charitable.

## Usage
After installing and activating this plugin, you can use the `[charitable_stat]` shortcode, with some additional parameters available.

For usage available with the existing integration in Charitable core, see [https://www.wpcharitable.com/documentation/stats-shortcode/](the documentation).

### Paramaters

Extra parameters added by this plugin are highlighted in bold.

- `display`: The type of data to display. The choices are:
- - `total` – The amount that has been raised. This is the default option.
- - `progress` – How much has been raised in relation to a goal. This will display a horizontal progress bar measuring the progress against a goal. Requires a goal parameter to be included too.
- - `donors` – The number of donors.
- - `donations` – The number of donations.
- - *`campaigns`* - The number of campaigns.
- `campaigns`: A comma-separated list of campaign IDs. If not provided, all campaigns will be included. By default, this is empty (all campaigns are included).
- `goal`: Required if display=progress. The goal that your progress is measured in relation to, without any currency symbols. By default, this is empty.

**Show total raised in a category**
```
[charitable_stat category=my-category] // Refer to category by slug
```

**Show total raised in a tag**
```
[charitable_stat tag=my-tag] // Refer to tag by slug
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
