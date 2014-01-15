# WP Image Size Limit

This is a fork of the [original plugin], with these changes:

[original plugin]: http://wordpress.org/plugins/wp-image-size-limit/

## MIME Type Pattern

Added option to limit the upload size only for the specified MIME types
based on a [regular expression pattern]. It defaults to "/^image\//", which
means anything starting with "image/", e.g. it will only enforce the size
limitation to "image/png", "image/jpeg", "image/foobar", but not
"application/something".

[regular expression pattern]: http://php.net/manual/en/reference.pcre.pattern.syntax.php
