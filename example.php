<?php
/*

The MIT License (MIT)

Copyright (c) 2015 Ondrej Koch

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/


require 'vendor/autoload.php';

use GuzzleHttp\Client;

class CkanApi {

    function __construct($url, $api_key){
      $this->client = new Client([
        'base_uri' => $url,
        'headers' => [
          'Authorization' => $api_key
        ]
      ]);
    }

    public function create_resource($data, $file){
      $request_data = [];
      foreach ($data as $key => $value){
        $request_data[] = ['name' => $key, 'contents' => $value];
      }
      $request_data[] = ['name' => 'upload', 'contents' => fopen($file, 'r')];

      $this->client->request(
        'POST',
        'action/resource_create',
        [
          'multipart' => $request_data
        ]
      );
    }

    public function update_resource($data, $file){
      $request_data = [];
      foreach ($data as $key => $value){
        $request_data[] = ['name' => $key, 'contents' => $value];
      }
      $request_data[] = ['name' => 'upload', 'contents' => fopen($file, 'r')];

      $this->client->request(
        'POST',
        'action/resource_update',
        [
          'multipart' => $request_data
        ]
      );
    }


}

function print_help(){
    print "Usage: php example.php [OPTIONS]\n";
    print "Runs CKAN resource create script with given options.\n";
    print "\n";
    print "OPTIONS:\n";
    print "  --url            URL of source CKAN.\n";
    print "  --api-key        API key for source CKAN.\n";
    print "  --name           Name of the resource.\n";
    print "  --package        Package id/name for the resource.\n";
    print "  --file           Filename to upload.\n";
    print "  --description    Description of the resource.\n";
    print "  --create         If present, it creates a resource instead of updating it.\n";
    print "  --resource       ID of the resource that should be updated (only in update mode).\n";

}

$longopts  = [
  'api-key:',
  'url:',
  'file:',
  'name:',
  'description:',
  'package:',
  'create',
  'resource:',
];


$options = getopt('', $longopts);
if($options){
  $ckan = new CkanApi($options['url'], $options['api-key']);

  $file = realpath($options['file']);
  $data = [
    'package_id' => $options['package'],
    'name' => $options['name'],
    'description' => $options['description'],
    'url' => '',
  ];

  if($options['create'] === false){
    print "Creating resource\n";
    $ckan->create_resource($data, $file);
  } else {
    print "Updating resource\n";
    $data['id'] = $options['resource'];
    $ckan->update_resource($data, $file);
  }
} else {
  print_help();
}
