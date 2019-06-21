# DispatchIT Shop Order Exporter

Provides an endpoint for fetching files to use with a New Zealand Couriers printer.

Write a desktop script to call `yoursite.com/dispatchitexporter`, and then download everything from
`yoursite/assets/_dispatchitexports` to the local folder for importing into dispatchit. This could be done via ssh connection. If you use this approach, it is recommended that you include `.htaccess` config to close the folder off from the web.

Alternatively, you can prevent writing output to the assets folder by adding the query param `yoursite.com/dispatchitexporter?nofile=true`.

Adding `yoursite.com/dispatchitexporter?dryrun=true` will both prevent files being written, and orders from being updated to mark them as having been exported for printing.

## Authentication

Requests can be authenticated via [Basic Auth](https://en.wikipedia.org/wiki/Basic_access_authentication), but *Please Note:* you should do basic auth over HTTPS to avoid authentication details from being snooped.

Create a new Member and add them to a Group with the permission "Access to address export API";

Authorise requests by providing the username and password credentials Basic Auth header.
