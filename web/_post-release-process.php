<pre>
<?php
passthru('whoami');
passthru('php --version');

print "\n";

chdir('..');

passthru('php bin/console assets:install web --symlink --relative 2>&1');
passthru('php bin/console doctrine:schema:update --force 2>&1');
passthru('php bin/console cache:clear --env dev --no-warmup 2>&1');
passthru('php bin/console cache:warmup --env dev 2>&1');
passthru('php bin/console assetic:dump --env dev 2>&1');
passthru('php bin/console cache:clear --env prod --no-warmup 2>&1');
passthru('php bin/console cache:warmup --env prod 2>&1');
passthru('php bin/console assetic:dump --env prod 2>&1');

print "\nDone\n";

?>