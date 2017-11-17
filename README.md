# Charitable Stat Shortcode
Adds a [charitable_stat] shortcode to Charitable.

## Usage
After installing and activating this plugin, you can use the `[charitable_stat]` shortcode.

**Default. Show total $ raised in all campaigns**
```
[charitable_stat]
```

**Same as default. Shows total $ raised in all campaigns**
```
[charitable_stat display=total]
```

**Show total number of donors in all campaigns**
```
[charitable_stat display=donors]
```

**Show total number of donations in all campaigns**
```
[charitable_stat display=donations]
```

**Show total $ raised in a specific campaign**
```
[charitable_stat campaigns=id]
```

**Show total $ raised in a set of campaigns**
```
[charitable_stat campaigns=id1,id2,id3]
```

**Show number of donors to a set of campaigns**
```
[charitable_stat campaign=id1,id2,id3 display=donors]
```

**Show number of donations for a set of campaigns**
```
[charitable_stat campaign=id1,id2,id3 display=donations]
```
