# Augstus
`gusto help`

```
Augustus is a static page generator and blog engine, written in php 5.4

Usage: gusto [options] <command> [<args>].

Available commands:
   add     Adds new entry to 
   rm      Remove an entry from
   edit    Alters an entry in
   list    Lists entries in
   build   Generates the static pages.
   help    Prints this help file.

Options:
   -f   Forced build.  Re-generates all pages regardless of checksum.
   -c   Clean build.  Wipes the build/ directory clean prior to generating
        static pages.

Examples:
   gusto add post
```