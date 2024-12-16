Tickets Please

Tickets Please is the project for Jeremy McPeak's "Laravel API Master Class" on laracasts.com

Tickets Please is a support ticket tracking and resolution backend API upon which developers can build their apps.
BIG TODO

I think this project will be a perfect opportunity to gain knowledge and practice in several other areas. So here is the big todo plan after finishing the course.

    Complete rewrite of these notes so it makes more sense to others. Not docs but lesson notes
    Write actual Docs for the API
    Add tests
    Refactor, add change logs, update docs
    Regarding #4, breaking changes are new versions, non breaking as minor versions, how to properly release major versions vs minor versions and document upgrade paths for major versions.

4 and 5 scare me a little.
Testing

Testing is not part of this course but I want to add it later. For a starting point on Pest and JSON:API endpoint testing look here That is assuming you are familiar with Pest. I just need to see the difference between testing application endpoints and testing JSON:API endpoints and I think I can get it figured out from there.
Designing the URL

The base url is http://tickets-please.test/api because this is in development. Ideally it will be something like https://ticketsplease.com/api in production Following are the end points we need for developers to be able to make good use of the API

    tickets
    users
    more to come...

Documenting the API

This is going to require some documentation so we know what some data returned from the API means. I personally would just give data meaningful names but I'll stick close to the structure provided by the course for now.

Status Codes for tickets: A = active C = completed H = hold X = canceled
Laravel API Setup

In a default Laravel app with no starter kit which is what we started with, we need to run artisan install:api. This will setup and run the personal_access_tokens migration and add the routes/api.php file. We need to manually add the HasApiTokens trait to the User model in order to use the token methods described later.
Versioning the API

The most straight forward thing and what we are doing in this course is to add a version parameter to the URL. http://tickets-please.test/api/v1/tickets

But also look at Modular Laravel by Mateus Guimarães on Laracasts.

Using apiResource instead of resource in the routes file will omit unnecessary routes such as create and edit which would show forms in a web application. Thanks Laravel :-)

Starting to go off track a little here because I'm using Laravel 11 and the course is on version 10. The new api_v1 routes file is loaded in bootstrap/app in the 'then' property
Auth Tokens

See AuthController::login to see how to create and assign a token to an authenticated user.

For revoking tokens there are multiple methods.

$request->user()->tokens()->delete() We don't want to do this unless a user is being completely banned for some reason along with any and all apps they may have created using their personal access tokens. This method will delete all tokens and break any app using them.

$request->user()->tokens()->where('id', $tokenId)->delete() This is ok in cases where we have the specific token id

$request->user()->currentAccessToken()->delete() This will get and delete the current token used for this request. So this is how to sign the user out in the logout method

For the purpose of learning without distraction, the additional parameters are added to the createToken method to give all abilities and expire the token after a month. This will be changed. Expiration should be shorter. A day or 30 minutes for example, depending on the real world use case for the API There is also a app wide config setting in config/sanctum.php that is set to null by default. you can enter a numeric value (minutes) here and it will override any tokens expires_at field in the database. token expiration docs
Designing Response Payloads

In order to adhere to the JSON API Specification, we can create a resource and build out our structure there. See resources/V1/TicketResource In Postman I have set the Accept header to application/vnd.api+json which is required for JSON:API as the media type. The Laravel resource automatically wraps our data in the data wrapper so we don't have to worry about including that in the returned array.

I'm not sure yet if it is correct but to get rid of squigglies (property accessed via magic method) from the IDE we can add @property doc comments to the resource class.

Resources are great for both api and web responses because they give us a place to include/omit properties from the response data. User data is a good example of where you don't want to provide any data other than what is needed to the front end (for security and data privacy)
Optional Parameters to Load Optional Data

The client (meaning the developer using our API) may not want certain optional data because it increases the size of the payload. When developing apps to use our API it is important to keep things like this in mind. Designing and developing an API requires a change in the mindset in this way. Our end user, client, customer is a developer, not the average home user.

With that said, the TicketResource was using the 'includes' property to include user data along with ticket data. This needs to be opt-in because the developer may not want to include user information in some scenarios.

See ApiController. This could have been a trait or a base controller. It can easily be switched over to a trait if that becomes a better way. One might argue that we don't want all of our controllers having access to the include() method for no reason.

So the method is now available to TicketController and the TicketResource conditionally returns 'includes' only if the user is loaded, via the Laravel provided method whenLoaded().

Next we optionally load user tickets for the user endpoint. As is, it is also loading the relationship with every ticket which is the user data. Since we already have the user data we need to think about how best to exclude this extra data when loading tickets with the user. Leaving it as is for now.

One more thing we did is to add/modify the 'links' properties in both TicketResource and UserResource to be consistent with the way Laravel presents pagination links.

Note: request()->get() causes IDE squigglies stating that there are unhandled exceptions thrown. But it does not throw an exception if the query parameter 'include' is missing so it might be an IDE problem. However, since we are getting a query string parameter I feel the more appropriate method is request()->query('include'). This method does not cause squigglies in the IDE either. So I changed it to use query in the ApiController.
Filters for Query Parameters

For the first filter, 'status' I added strtoupper in the filter because we used upper case in the database. This way the user can type 'c' or 'C' for the status. Too much flexibility in an API could cause complexity or even break things but I think this is simple enough to allow it.

Next we setup filters for title, dates, date ranges, and multiple status. Example here is tickets including the author with status C or X with a title that has 'foo' in it and a createdAt date within the supplied range. http://tickets-please.test/api/v1/tickets?include=author&filter[status]=C,X&filter[title]=*foo*&filter[createdAt]=2024-03-04,2024-05-03

Side Note about dates, sqlite, and Jetbrains IDEs: They will screw up the date and break your application if you do not set your source driver up correctly for sqlite. date_class = TEXT date_string_format = yyyy-MM-dd HH:mm:ss see jb issue here
Filtering Nested Resources

First commit for this section we changed a few things to make the distinction between users and authors.

Next we create a nested controller AuthorTicketsController to handle getting tickets belonging to a particular author http://tickets-please.test/api/v1/authors/1/tickets And we can still use our filters for the tickets.
Sorting Data

First we get the User model and Author controller up to date on filtering. http://tickets-please.test/api/v1/authors?filter[id]=1,5,11 to filter authors by id Of course the original url for a single author authors/id still works. This just gives us a way to get multiple authors by id.

Now we will set up sorting such as http://tickets-please.test/api/v1/tickets?sort=title,status Defaults to ASC or prefixed with '-' for DEC http://tickets-please.test/api/v1/tickets?sort=-title,-status
Creating Resources with Post Requests

Remember we are using the sanctum middleware on our routes. This means in the authorise method of our request classes we can simply return true. In other applications this would be where we make sure the user is logged in but in this case they can't get past the route without being logged in first.

This is also where we would check roles at a later point to determine if a logged in user is able to make a specific request but for now, any logged in user can perform any of our POST requests.

Validation notes: Under the hood, Laravel uses PHP methods so this is not necessarily a fault of Laravel but in order to cover ALL edge cases for the id (non negative, non zero, non decimal, integer type, non boolean) we have to combine a few rules. I think it's integer that allows true/false and numeric that allows decimal? Google it though. It's true. Also waiting to see where we go with this but I think we need to check the id exists in the db unless the act of creating a ticket triggers, creating an account or something. Not sure why it's being left out as a validation rule. Will revisit. 'data.relationships.author.data.id' => ['required', 'integer', 'numeric', 'min:1'], Comments in the lesson suggest using the validation check for exists and changes will come in future lessons but for now, we have complete control over the returned response by checking it in the controller in a try/catch. We are returning a 200 response as a sort of security through obscurity because attackers tend to use automated tools that search for error statuses. It is debatable whether to do this or not. Another thing to consider, research, and revisit. Also look at AuthController where we used error instead of success message.

We make it possible to create a new ticket both from the TicketController and from the AuthorTicketController. Remember this when documenting the API. 2 ways to create tickets.
Deleting with a Delete Request

In the case of tickets we go ahead and return a 404 error since this is the expected behavior in a typical system. We aren't trying to hide any implementation details from hackers here.

We provide the ability to delete a ticket in both the TicketController and the AuthorTicketController See @Bionik6 APIExceptions class in lesson comments I'm sure I'll be looking into this change which will change how and where I use try/catch blocks in the controllers.
Replace a Resource with PUT

My first question is always "why would I want to replace it?"

To distinguish between PATCH/PUT requests we create an actual replace() method instead of running them both through the update method.

Replacing the created_at/updated_at fields is something to consider. But in this case we are not. Had to reverse how to show the description field in TicketResource because it wasn't showing for the replace method. Now we only stop it from showing on the 2 routes where we don't want it to show. !$request->routeIs(['tickets.index', 'authors.tickets.index']),
TODO Security

roles, policies, etc. At the moment any user can make all of their tickets belong to other users Several access control lessons coming up soon.
Update Resources with a Patch Request

We implemented the ability to use a PATCH request to update tickets. We also cleaned up by creating a BaseTicketRequest with the mappedAttributes method. In this way we not only cleaned up those arrays of attributes in the controllers but also provided a way to do both PUT and PATCH requests with a one-liner and determine which attributes are in the request.
Using Policies for User Authorization

Been waiting for this. Up to this point our users can do too much. Just to list a few issues, A user should not be able to update/replace another users ticket A user should not be able to delete another users ticket A user should not be able to change their own ticket to another users id A user may need to have rules about when they can delete their own tickets A user may also need to have rules about which fields they can update in their own tickets. Maybe admin or ticket-master (silly job title) should be the only one able to delete or update It all depends on business needs but as an API builder who does not know the clients needs, I have to try to think in a different way than I would if I were building the front end. Hmmm... some thought is required.

There are a lot of different authorizations to consider and write and they would make the controller really messy if we put them in there. So we will use policies.

Laravel 11 policies will be automatically registered, providing they follow convention Models/Ticket.php will need TicketPolicy.php. Also the policy must be in the app/Policies directory.

One issue is making sure our model and the authorize method knows which version TicketPolicy to use. We fix that with the isAble custom method in the base ApiController class and our protected $policyClass in the TicketController.

Laravel 11 authorize method is no longer available on the controller. There are a couple of ways to handle it. For now I'll go with Gate facade in ApiController. Gate::authorize()

authorize is also available on an authenticated user object so request->user->authorize should work. Need to test.

I ran into issues trying to use a versioned policy. In Laravel 11 the array of arguments passed to authorize as the second arg is simply args that go to the policy method. They have no effect on which policy is used. This is determined by the policy that is bound to the model when registered in AppServiceProvider. However, I found that I can register multiple policies to the same model and use Gate::policy to set the one used at runtime. See AppServiceProvider and the isAble method where i set the correct policy to use based on the $this->policyClass property in the controller. This allows me to have V1/TicketPolicy and V2/TicketPolicy

Update: There is another way by @Coni in the lesson discussion where we can set the policy to use in the constructor of ApiController
Controlling Access with Token Abilities

See new Permissions/V1/Abilities and how it is used in TicketPolicy and how it is included in the token as Abilities::getAbilities($user) in AuthController.

Again we partially versioned something. I'll need to look into the best way to have a V2 Permissions. Though we may not need a V2 for permissions. But since we did version it, we need to have a way to dynamically include the correct version in the AuthController. Maybe it is as simple as versioning AuthController but the more we version, the more it seems we might as well just create a new project called Tickets Please 2.0

Anyway, I renamed the ticket constants like UpdateTicket to UpdateAnyTicket to make it clear and consistent with UpdateOwnTicket
Applying Granular Permissions

Good for demo but not sure that I like how we use the ability in UpdateTicketRequest. It is not very clear. I think I need to go through later and consider renaming the constants. Also I'm thinking abilities can be to not be able NotDoThis and then can be used as negatives if it makes it more clear in certain situations. Can I have the ability to no be able to do something ?

Noteworthy, we made changes in the UpdateUserRequest and StoreUserRequest that called for some changes in TicketController. The way I have done policy versioning in ApiController, required me to put in a ternary check for cases where there is no ticket to pass but we pass Ticket::class. If it's already a fqn I don't need to get the fqn
Implementing the AuthorTicketsController

The functionality is basically the same as TicketController, with a few small differences. The author id is included in the request url so we already have that. So we don't need to include it in the actual request.

StoreTicketRequest: We don't actually need to check the route for tickets.create but we do need to merge the author id into the request so we use prepareForValidation

We have to update our mappedAttributes to accept a passed in user_id as 'author' using array_merge we add the author id but also have the ability if we wanted to, to override existing attributes.

We did some refactoring of how the ticket was retrieved from the database in order to clean up and get rid of the conditional checks against the user_id.

After the refactor, when we try to update from AuthorTickets and use an incorrect user id, it never gets past the db query because we get a ModelNotFoundException instead of using our policy which would throw a AuthorizationException. So the client gets the message 'not found' instead of 'unauthorized' which is fine because a ticket with that id for that user id was not found. Just might not be as helpful when you know the ticket does exist? It just doesn't belong to the user.

Side Note: Need to go through app and determine which errors need 401 vs 403 Also look at merging Update and Replace request and the corresponding messages. Methods described above and in the different lesson discussions in Laravel 11 would allow me to clean up a lot and completely get rid of the isAble method in favor of Laravel authorize method if I get rid of the replace method and revert back to an apiResource route for all controller methods.
Managing Users

For starters, the authors controller is returning all users. Who says all users are authors? So we change to using a select and join to get the users who have created tickets.

Lot going on. Look back at the commit, update notes later. User controller, requests, resources all are a bunch of copy/paste/modify from Tickets Added UserFilter even though for now it is identical to AuthorFilter. Ya never know. Well actually, might make some changes because there is a difference between users and authors regarding having published tickets.

Changed the authors route because authors won't be creating, updating, or deleting authors.

Registered the policy inside AppServiceProvider but it has occurred to me that is what I am doing in ApiController in the isAble method. So I may not need to register them in the service provider at all. Will test and update.

Dont think we should be telling the world which users are admins when listing all users. Will fix
The Principle of Least Privilege

Regardless of who the user is in a system, assign them the most restrictive permissions possible. As they need more authority, grant only that authority, always maintaining the least privilege or most restrictive permissions possible.

In StoreTicketRequest we make the default rule the most restrictive. Then for admin/manager users who have the CreateTicket ability, we modify the rule. Same situation in UpdateTicketRequest.

We refactor the isAble method and move the try/catch into it so we can clean up TicketController. Not sure what this has to do with security and Principle of Least Privilege but it's a good refactor. Will do the same for all controllers.
Handling Errors

By defining exception handlers and then utilizing route model binding and making some changes to json response errors we were able to make errors consistent in their responses to the client. This also gives us the ability to keep tight control over what information gets returned to the client which is a plus for security/privacy.

There were a few differences from the course because of Laravel 11. Exceptions handlers/renderers are defined in bootstrap/app.php I started out by defining all of them there. Then I moved them out to an ApiException class. I made everything static so the code in app.php could be cleaner.

All controllers are updated to use route model binding which will throw the NotFoundHttpException when a ModelNotFoundException is encountered. So I type hinted both exceptions in a single handleNotFoundException handler.

We also added the notAuthorized method to the ApiResponses trait in order to use it in the controllers
Documenting Your API with Scribe

I'm testing out code-lts/doctum instead. Laravel.com uses Doctum to generate the API docs for the framework. Then they manually create the detailed docs with code samples as markdown files. You can see how by reading through the source code at laravel/docs and laravel/laravel.com

See the docbuilder/doctum dir in this repo and the README.md located there to see how I've done it in this project.

Unfortunately from what we learned about Scribe in this lesson and from what I am seeing with doctum and looking at the docs for phpDocumentor it looks like everyone just does their own thing instead of following a standard for php docBlocks. Some things are used by one solution and ignored by another. And all of them have IMHO terrible documentation. It's like something I wrote :-)

I like the way Laravel does it because it separates the API from guides, code samples, etc. Also doctum docs are prettier to me and don't need any styling updates (clean and simple API docs). And using markdown files for the rest will give me complete freedom for explanations, code examples, notes, caveats, or anything I can think of without trying to figure out how to make it show up in a docblock or adding/modifying my source code for it. And it gives me complete freedom for styling the display of the markdown docs.

You can see in this commit where I've left out some scribe specific annotations and added in others that work for doctum. github flavor code blocks work for both Doctum and phpDocumentor(according to docs) but I haven't tested and don't see it anywhere in the documentation for Scribe.

Notice in TicketController for the index method I use the code block because doctum ignores @queryParam. Also the return type is AnonymousResourceCollection but I want the api docs to have a clickable reference to TicketResource so I used the @see property which doctum uses to add a "See also TicketResource" link to the docs. Same for TicketFilter

While working on TicketController I found that doctum build cache is not working for methods and properties from the included trait or from the extended parent. I have to delete the cache and build directories if I make changes in one of those files before regenerating the docs. This may be a known issue. I haven't researched yet. But The scripts that laravel uses to generate theirs does do a clean up process to delete these directories both before and after.

I fixed the parts where Scribe pointed out to us flaws in our rules. I do like this part about Scribe. However, in doctum the rules don't show because it is purely documenting the API and not trying to generate both usage and API docs all together. In other words I feel strongly that there are 2 things I should not be including in a pure API documentation. Those are implementation details and business logic. Some small helpful hints are OK. But I think at most I should use @see to display a link to the docs page where I render my markdown docs like Laravel does.

In the end I guess it comes down to the project and the type of docs you want to generate. I'm a fan of separate doctum and markdown for now.

TODO: View laravel framework source for docblock examples, update all docblocks in this project TODO: Write markdown docs and display them at '/docs. TODO: Change api docs route from 'docs' to '/docs/api' TODO: Use Pest to write tests
