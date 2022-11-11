# This dockerfile is used to run the tests by the gitlab runner.
FROM mysql:8.0.19

# left out ONLY_FULL_GROUP_BY below to enable hacky group-by functionality in SimpleReadModelDBALRepository
# this is no problem since we have this on the real server as well.
RUN echo sql-mode=STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION >> /etc/mysql/my.cnf

# This plugin allows mysqli and PFO to connect, they do not have support for caching_sha2_password yet.
# https://mysqlserverteam.com/upgrading-to-mysql-8-0-default-authentication-plugin-considerations/
# We might be able to drop this when upgrading to 7.4.
# https://bugs.php.net/bug.php?id=78981
RUN echo default-authentication-plugin=mysql_native_password >> /etc/mysql/my.cnf
