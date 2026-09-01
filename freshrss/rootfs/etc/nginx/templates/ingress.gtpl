server {
    listen {{ .interface }}:8099 default_server;

    include /etc/nginx/includes/server_params.conf;

    allow   172.30.32.2;
    deny    all;

    location ~ ^.+?\.php(/.*)?$ {
        include /etc/nginx/includes/php.conf;

        # Home Assistant serves the app from below the root of its own domain,
        # under a path it hands out per session. FreshRSS builds its absolute
        # URLs around X-Forwarded-Prefix, so the path is passed on under that
        # name. Everything else it renders is relative and needs no help.
        fastcgi_param HTTP_X_FORWARDED_PREFIX $http_x_ingress_path if_not_empty;

        {{ if .auto_login }}
        # Home Assistant has already established who is asking by the time a
        # request reaches Ingress, so FreshRSS is told to accept that, and each
        # person gets an account of their own. The name is worked out from the
        # Home Assistant user by the maps in nginx.conf.
        #
        # Unlike the header variants of this, REMOTE_USER cannot be reached by
        # a client: NGINX prefixes every incoming header with HTTP_, so nothing
        # a browser sends can land here. That is also why this needs no entry
        # in FreshRSS' trusted_sources.
        fastcgi_param REMOTE_USER $freshrss_ingress_user;
        {{ end }}
    }
}
