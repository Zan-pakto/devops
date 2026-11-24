# Puppet Site Manifest
# Applied by Puppet Server / Puppet Agent to manage target nodes

node 'web-target.devops.lab', 'web-target', default {
  notify { 'Applying Apache Web Configuration via Puppet': }
  include apache_web
}
