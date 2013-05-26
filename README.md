# Augstus
## `gusto help`

```
Augustus is a static page generator and blog engine, written in php 5.4

Usage: gusto [options] <command> [<args>].

Available commands:
   add         Adds new entry to 
   rm          Remove an entry from
   edit        Alters an entry in
   list        Lists entries in
   build       Generates the static pages.
   configure   List and set configuration options.
   help        Prints this help file.

Build options:
   -f   Forced build.  Re-generates all pages regardless of checksum.
   -c   Clean build.  Wipes the build/ directory clean prior to generating
        static pages.  Must be used together with -f

Examples:
   gusto add post          Add new post.
   gusto -cf build         Clean build directory and generate static pages.
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

  * `$ ./gusto configure` edit the options you want.

  * `$ ./gusto new post` follow the CLI prompts.

  * `$ ./gusto build`

  * `$ ./gusto publish`

  * `$ cd ..`

* `git add .`

* `git commit -m "Wrote some blog posts"`

* `git push`
