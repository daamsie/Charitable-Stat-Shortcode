# Charitable Stat Shortcode
The `[charitable_stat]` shortcode is now available in Charitable core.

Installing this plugin will give you access to some new features, not currently available with Charitable.

## Usage
After installing and activating this plugin, you can use the `[charitable_stat]` shortcode, with some additional parameters available.

For usage available with the existing integration in Charitable core, see [the documentation](https://www.wpcharitable.com/documentation/stats-shortcode/).

### Paramaters

Extra parameters added by this plugin are highlighted in bold.

- `display`: The type of data to display. The choices are:
  - `total` – The amount that has been raised. This is the default option.
  - `progress` – How much has been raised in relation to a goal. This will display a horizontal progress bar measuring the progress against a goal. Requires a goal parameter to be included too.
  - `donors` – The number of donors.
  - `donations` – The number of donations.
  - **`campaigns` - The number of campaigns.**
- `goal`: Required if display=progress. The goal that your progress is measured in relation to, without any currency symbols. By default, this is empty.
- `campaigns`: A comma-separated list of campaign IDs. By default, this is empty. If the `category`, `type` and `tag` parameters are all empty, this will result in all campaigns being included.
- **`parent_id`: Filter to only show stats for campaigns that share a specific parent campaign, indentified by campaign ID. Include multiple parent IDs with a comma-separated list.**
- **`category`: Filter to only show stats for campaigns in a specific category, identified by slug. Include multiple categories with a comma-separated list.**
- **`tag`: Filter to only show stats for campaigns with a specific tag, identified by slug. Include multiple tags with a comma-separated list.**
- **`type`: Filter to only show stats for campaigns of a specific type, identified by slug. Options include `admin`, `fundraiser`, `personal-cause`, `individual`, `team` or `team-member`. Include multiple types with a comma-separated list.**
- **`include_children`: Whether to include stats for the campaigns listed in `campaigns` _and_ any child campaigns. Set to `true` to include child campaigns. Set to `false` to exclude child campaigns. Defaults to `true`.**

### Examples

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

**Show total number of campaigns**
```
[charitable_stat display=campaigns]
```

**Show total number of fundraisers for a specific parent campaign**
```
[charitable_stat display=campaigns parent_id=123]
```

**Show amount raised by teams for a specific parent campaign**
```
[charitable_stat display=total parent_id=123 type=team]
```
