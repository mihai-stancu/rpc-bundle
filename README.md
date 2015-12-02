RpcBundle
================================================================================

A symfony RPC Client and Server implementation which integrates the symfony/serializer.

The RPC server uses a symfony controller to execute methods of the exposed services.

The RPC client uses proxy classes -- generated based on interfaces or abstract classes
-- whose abstract methods will be forwarded to the remote RPC server.

Due to integration with symfony/serializer the RPC server/client supports many possible
normalizers and encoders.
