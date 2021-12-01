This is a sample Laminas MVC project, that we set-up or testing purposes. It is configured to be able to run a single
job.

To run:

1. Run ./install.sh. This will make sure a copy of this library is placed in a sibling directory `./lib`. This hack is
   required, because Composer cannot add a repository that is inside of a parent directory.
2. Run `test.php` from within the `./app` folder. This will fail with exit code 0 if and only if the test succeeded.

Note that `laminas-cli` will bootstrap the application automatically when `vendor/bin/laminas` is executed in this
directory. That is because it will find the `config/container.php` file.
