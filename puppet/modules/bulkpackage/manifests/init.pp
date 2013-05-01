define bulkpackage($packages) {
    $packages_join = inline_template('<% packages.each do |package| %><%= package %> <% end %>')
    exec { "apt-get -y install $packages_join && apt-get clean":
        onlyif  => "dpkg-query -W -f='\${Status}\\n' $packages_join 2>&1 |grep -v installed",
        path    => "/bin:/sbin:/usr/bin:/usr/sbin",
        timeout => 7200,
    }
}