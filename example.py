#!/usr/bin/python -tt
"""
Example script to show how to create or update resource in CKAN from file.

Created for Magistrát hlavního města Prahy

Licence: MIT

Installation:
Only dependency is python and requests library, which can be installed by
> pip install requests
"""

import requests
import json
import os

from getopt import gnu_getopt
from sys import argv, exit

class CkanApi:
    def __init__(self, url, key):
        self.url = url
        self.key = key
        self.headers = {'Authorization': self.key}

    def create_resource(self, data, files):
        r = requests.post(self.url+'action/resource_create',
                data=data,
                files=files,
                headers=self.headers)
        return r.json()

    def update_resource(self, data, files):
        r = requests.post(self.url+'action/resource_update',
                data=data,
                files=files,
                headers=self.headers)
        return r.json()



def print_help():
    print 'Usage: '+argv[0]+' [OPTIONS]'
    print 'Create or update CKAN resource'
    print ''
    print 'OPTIONS:'
    print '  --help, -h               Display this help'
    print ''
    print '  --url, -s                URL of CKAN server api'
    print '  --api-key, -S            API key for CKAN'
    print ''
    print '  --package, -p            Package (dataset) containing resource'
    print '  --resource, -r           Resource id'
    print ''
    print '  --name, -n               Resource name'
    print '  --description, -d        Resource description'
    print ''
    print '  --create, -c             Create resource instead of updating'
    print ''
    print '  --file, -f               File to be uploaded to resource'
    print ''
    print ''
    print 'EXAMPLES:'
    print '  Create new resource:'
    print '  '+argv[0]+' --create --url http://ckan.example.com/api/3/ --api-key xxx --package package-id --name "New Resource" --description "My description" --file example.csv'
    print '  Update resource with new data:'
    print '  '+argv[0]+' --url http://ckan.example.com/api/3/--api-key xxx --package package-id --resource resource-id --file example.csv'
    print '  Update resource with new data and name:'
    print '  '+argv[0]+' --url http://ckan.example.com/api/3/ --api-key xxx --package package-id --resource resource-id --name "new name" --file example.csv'



if __name__ == '__main__':

    opts, args = gnu_getopt(argv, 'hs:S:p:r:n:d:cf:', 
            ['help', 'url=', 'api-key=', 'package=',
             'resource=', 'name=', 'description=', 'create', 'file='])

    server = ''
    api_key = ''
    package_id = ''
    resource_id = ''
    resource_name = ''
    filename = ''
    description = ''
    create = False

    for o, a in opts:

        if o in ('-s', '--url'):
            server = a
        elif o in ('-S', '--api-key'):
            api_key = a
        elif o in ('-p', '--package'):
            package_id = a
        elif o in ('-r', '--resource'):
            resource_id = a
        elif o in ('-n', '--name'):
            resource_name = a
        elif o in ('-d', '--description'):
            description = a
        elif o in ('-f', '--file'):
            filename = a
        elif o in ('-c', '--create'):
            create = True
        elif o in ('-h', '--help'):
            print_help()
            exit()

    api = CkanApi(server, api_key)

    files = [('upload', file(filename))]
    data = {
        'package_id': package_id,
        'url': '',
    }

    if description:
        data['description'] = description

    if resource_name:
        data['name'] = resource_name

    if create:
        res = api.create_resource(data, files)
    else:
        data['id'] = resource_id
        res = api.update_resource(data, files)

    if res['success'] == False:
        print "%s: %s" % (res['error']['__type'], res['error']['message'])
        exit(1)


# vim:set sw=4 ts=4 et:
# -*- coding: utf-8 -*-
