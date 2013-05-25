# Augstus
## `gusto help`

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

## Recommended installation and deployment using GitHub Pages, 
with Augustus as a submodule.

* Make new github repo `username.github.io`

* `$ git clone https://github.com/username/username.github.io.git`

* `$ echo "yourfancydomain.com" > CNAME`

* `$ git add CNAME`

* `$ git commit -m "Added CNAME file."`

* `$ git push`

* `$ git submodule add https://github.com/xles/augustus.git .gusto`

* `$ cd .gusto/`

* * `$ ./gusto configure` edit the options you want.

* * `$ ./gusto new post` follow the CLI prompts.

* * `$ ./gusto build`

* * `$ ./gusto publish`

* * `$ cd ..`

* `git add .`

* `git commit -m "Wrote some blog posts"`

* `git push`
