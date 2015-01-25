<?php

/**
 * @small
 */
class VariantBuilderTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $variants = new PhpBrew\VariantBuilder;
        ok($variants);

        $build = new PhpBrew\Build('5.3.0');
        $build->enableVariant('debug');
        $build->enableVariant('icu');
        $build->enableVariant('sqlite');
        $build->enableVariant('xml_all');
        $build->enableVariant('apxs2','/opt/local/apache2/apxs2');

        $build->disableVariant('sqlite');
        $build->disableVariant('mysql');
        $build->resolveVariants();

        $options = $variants->build($build);
        ok( in_array('--enable-debug',$options) );
        ok( in_array('--enable-libxml',$options) );
        ok( in_array('--enable-simplexml',$options) );

        ok( in_array('--with-apxs2=/opt/local/apache2/apxs2',$options) );

        ok( in_array('--without-sqlite3',$options) );
        ok( in_array('--without-mysql',$options) );
        ok( in_array('--without-mysqli',$options) );
        ok( in_array('--disable-all',$options) );
    }

    public function testEverything()
    {
        $variants = new PhpBrew\VariantBuilder;
        ok($variants);

        $build = new PhpBrew\Build('5.6.0');
        $build->enableVariant('everything');
        $build->enableVariant('opcache');
        $build->disableVariant('openssl');
        $build->resolveVariants();
        $this->assertArraySubset(
            array(
                '--disable-all',
                '--enable-phar',
                '--enable-session',
                '--enable-short-tags',
                '--enable-tokenizer',
                '--with-pcre-regex',
                '--with-zlib=/usr',
                '--enable-opcache',
                '--with-sqlite3',
                '--with-pdo-sqlite',
                '--with-mysql=mysqlnd',
                '--with-mysqli=mysqlnd',
                '--with-pdo-mysql=mysqlnd',
                '--with-pgsql=/usr/bin/psql',
                '--with-pdo-pgsql=/usr/bin/psql',
                '--enable-pdo',
                '--enable-mbstring',
                '--enable-mbregex',
                '--enable-bcmath',
                '--with-bz2=/usr',
                '--enable-calendar',
                '--enable-cli',
                '--enable-ctype',
                '--enable-dom',
                '--enable-fileinfo',
                '--enable-filter',
                '--enable-shmop',
                '--enable-sysvsem',
                '--enable-sysvshm',
                '--enable-sysvmsg',
                '--enable-json',
                '--with-mhash',
                '--with-mcrypt=/usr',
                '--enable-pcntl',
                '--with-pcre-regex',
                '--with-pcre-dir=/usr',
                '--enable-phar',
                '--enable-posix',
                '--with-readline=/usr',
                '--with-libedit=/usr',
                '--enable-sockets',
                '--enable-tokenizer',
                '--enable-dom',
                '--enable-libxml',
                '--enable-simplexml',
                '--enable-xml',
                '--enable-xmlreader',
                '--enable-xmlwriter',
                '--with-xsl',
                '--with-libxml-dir=/usr',
                '--with-curl=/usr',
                '--enable-zip',
                '--without-openssl',
            ),
            $variants->build($build)
        );
    }


    public function testMysqlPdoVariant()
    {
        $variants = new PhpBrew\VariantBuilder;
        ok($variants);

        $build = new PhpBrew\Build('5.3.0');
        $build->enableVariant('pdo');
        $build->enableVariant('mysql');
        $build->enableVariant('sqlite');
        $build->resolveVariants();

        $options = $variants->build($build);
        ok( in_array('--enable-pdo',$options) );
        ok( in_array('--with-mysql=mysqlnd',$options) );
        ok( in_array('--with-mysqli=mysqlnd',$options) );
        ok( in_array('--with-pdo-mysql=mysqlnd',$options) );
        ok( in_array('--with-pdo-sqlite',$options) );
    }

    public function testAllVariant()
    {
        $variants = new PhpBrew\VariantBuilder;
        ok($variants);

        $build = new PhpBrew\Build('5.3.0');
        $build->enableVariant('all');
        $build->disableVariant('mysql');
        $build->disableVariant('apxs2');
        $build->resolveVariants();

        $options = $variants->build($build);
        ok( in_array('--enable-all',$options) );
        ok( in_array('--without-apxs2',$options) );
        ok( in_array('--without-mysql',$options) );
    }

    /**
     * A test case for `neutral' virtual variant.
     */
    public function testNeutralVirtualVariant()
    {
        $variants = new PhpBrew\VariantBuilder;
        ok($variants);

        $build = new PhpBrew\Build('5.3.0');
        // $build->setVersion('5.3.0');
        $build->enableVariant('neutral');
        $build->resolveVariants();

        $options = $variants->build($build);
        // ignore `--with-libdir` because this option should be set depending on client environments.
        $actual = array_filter($options, function ($option) {
            return !preg_match("/^--with-libdir/", $option);
        });

        is( array(), $actual );
    }
}
