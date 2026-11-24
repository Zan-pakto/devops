# Puppet Module: apache_web
# Installs and manages Apache2 HTTP Server and PHP Runtime with SQLite support

class apache_web {
  # Install Apache web server and PHP runtime dependencies
  package { ['apache2', 'php', 'libapache2-mod-php', 'php-sqlite3', 'sqlite3']:
    ensure => installed,
  }

  # Ensure the apache2 service is enabled and running
  service { 'apache2':
    ensure     => running,
    enable     => true,
    hasrestart => true,
    hasstatus  => true,
    require    => [
      Package['apache2'],
      Package['libapache2-mod-php'],
    ],
  }

  # Ensure document root directory exists with correct permissions
  file { '/var/www/html':
    ensure  => directory,
    owner   => 'www-data',
    group   => 'www-data',
    mode    => '0775',
    require => Package['apache2'],
  }

  # Ensure data directory for SQLite database storage
  file { '/var/www/html/data':
    ensure  => directory,
    owner   => 'www-data',
    group   => 'www-data',
    mode    => '0775',
    require => File['/var/www/html'],
  }
}
