# RPC Requests without the hassle

* #### Computers should talk to computers, people should talk to people

* #### You shouldn't need to care about encoding formats
 * JSON, XML, BSON, MsgPack, IGBinary

* #### Or about RPC protocols 
 * JSON-RPC, JSON-XSP, SOAP, XML-RPC, WSDL, WAMP, REST, JSend

* #### Or about communication protocols
 * HTTP, AMQP, ZMQ, WebSockets, TCP


---
## 3rd Party API spec becomes an interface

#### You start to define interfaces for each service described in the documentation you've received.
#### You can use abstract classes with only a few methods declared as abstract.

```php
interface XyzShipmentInterface
{
	/**
	 * @return int|string A shipment ID / barcode
	 */
	public function create();

	/**
	 * @param int|string The shipment ID / barcode
	 * @param 
	 */
	public function addAwb($shipmentId, XyzAwb $awb);

	/**
	 * @param int|string The shipment ID / barcode
	 */
	public function close($shipmentId);
}
```

---
## ...for ...each ...service

```php
interface XyzAwbInterface
{
	/**
	 * @param int|string The awb ID / barcode
	 *
	 * @return int|string The Awb processing status 
	 */
	public function status($awbBarcode);

	/**
	 * @param int|string $format The file format to download
	 *
	 * @return string|binary The PDF of the Awb (as a binary string)
	 */
	public function download($format);
}
```

---
## ...plain old PHP classes for params

#### You can use public properties, getters/setters, anything that Symfony/Serializer can normalize/denormalize

```php
class XyzAwb
{
	public $barcode;

	public $weight;
	public $length;
	public $width;
	public $height;
	public $value;

	public $status;
	public $file;
}
```

---
## Then you define your **interface** as a service

#### ...wait what? --- don't worry, we'll come back to this

```yaml

services:
	dms.courier.xyz_shipment:
		class: Courier\APISpec\XyzShipmentInterface

	dms.courier.xyz_awb:
		class: Courier\APISpec\XyzAwbInterface

```

---
## ...and just work with it as a services in your code:

```php

$shipmentService = $this->get('dms.courier.xyz_shipment');
$shipmentId = $shipmentService->create();

$awbService = $this->get('dms.courier.xyz_awb');

foreach ($awbs as $awb) {
	$barcode = $shipmentService->addAwb($shipmentId, $awb);
	$awb->barcode = $barcode;
	$content = $awbService->download($barcode);

	file_put_contents($awb->file, $content);
}

/* ... later on ... */

$awbService = $this->get('dms.courier.xyz_awb');

foreach ($awbs as $awb) {
	$awb->status = $awbService->status($awb->barcode)
}
```

---
## So how does it work?

#### Let's go back to the service declarations and add some configs:

```yaml
services:
	dms.courier.xyz_connection:
		class: MS\RpcBundle\Connection\ConnectionFactory
		arguments: 
			- [ http, json-rpc, json ]
			- { base_uri: 'http://api.xyz.com' }
		tags:
			- { name: ms.rpc.connection }

	dms.courier.xyz_shipment:
		class: Courier\APISpec\XyzShipmentInterfacet
		tags:
			- name: ms.rpc.proxy
			  connection: dms.courier.xyz_connection

	dms.courier.xyz_awb:
		class: Courier\APISpec\XyzAwbInterface
		tags:
			- name: ms.rpc.proxy
			  connection: dms.courier.xyz_connection 
```

---
## Tie it all together

* A connection service is configured to run via HTTP and accept JSON-RPC with messages encoded as JSON.
  
* Our initial "interface" services are tagged as "ms.rpc.proxy" and have a connection service at hand.
  
* A concrete implemented proxy class has been generated for (/instead of) our initial "interface" services (the proxy implements / extends our initial "interface" services).
  
* The proxy class will handle passing a function call to the connection class.
  
* Data associated with a function call will be wrapped into an JSON-RPC Request object.
  
* The service name, function name and parameters of the function call are all included in the RPC object.
  
* The connection object will then try to send the RPC request to the chosen endpoint (i.e.: make an HTTP request with a JSON-RPC body content).
  
* The Symfony/Serializer (+ some extensions) handle transforming our RPC object into an adequate string for the chosen protocol.
  
