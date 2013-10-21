%define		plugin	check_sphinx
%define		php_min_version 5.0.0
%include	/usr/lib/rpm/macros.php
Summary:	Nagios plugin to check Sphinx search engine status
Name:		nagios-plugin-%{plugin}
Version:	1.0
Release:	3
License:	GPL v2+
Group:		Networking
Source0:	%{plugin}.php
Source1:	%{plugin}.cfg
BuildRequires:	rpm-php-pearprov >= 4.4.2-11
BuildRequires:	rpmbuild(macros) >= 1.461
Requires:	nagios-common
Requires:	nagios-plugins-libs
Requires:	php(core) >= %{php_min_version}
Requires:	php(sphinx)
BuildArch:	noarch
BuildRoot:	%{tmpdir}/%{name}-%{version}-root-%(id -u -n)

%define		_sysconfdir	/etc/nagios/plugins
%define		plugindir	%{_prefix}/lib/nagios/plugins

%description
Nagios plugin to check Sphinx search engine status.

%prep
%setup -qcT

%install
rm -rf $RPM_BUILD_ROOT
install -d $RPM_BUILD_ROOT{%{_sysconfdir},%{plugindir}}
install -p %{SOURCE0} $RPM_BUILD_ROOT%{plugindir}/%{plugin}
cp -p %{SOURCE1} $RPM_BUILD_ROOT%{_sysconfdir}/%{plugin}.cfg

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(644,root,root,755)
%attr(640,root,nagios) %config(noreplace) %verify(not md5 mtime size) %{_sysconfdir}/%{plugin}.cfg
%attr(755,root,root) %{plugindir}/%{plugin}
