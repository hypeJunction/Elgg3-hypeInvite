# hypeInvite — Plugin Architecture (Elgg 5.x)

An interface for inviting new users and managing group invitations on Elgg sites.

## Plugin ID

`hypeinvite`

## Entity Types

| Type | Subtype | Class | Description |
|------|---------|-------|-------------|
| `object` | `user_invite` | `Invite` | An invitation to the site for a specific email |
| `object` | `group_invite` | `Invite` | A group invitation for a specific email |
| `object` | `user_invite_request` | `InviteRequest` | A user's request to be invited |

## Directory Structure

```
hypeinvite/
├── classes/hypeJunction/Invite/
│   ├── Bootstrap.php                      # Plugin bootstrap (conditional hypeHero menu wiring)
│   ├── Invite.php                         # ElggObject subclass for user/group invites
│   ├── InviteRequest.php                  # ElggObject subclass for invite requests
│   ├── InviteService.php                  # Business logic: create/validate/get invites
│   ├── AcceptFriendInvitesOnRegistration.php  # Event: accept:invite → create friendships
│   ├── AcceptGroupInvitesOnRegistration.php   # Event: accept:invite → join groups
│   ├── ProcessUserInvitesOnRegistration.php   # Event: create:user → process pending invites
│   ├── RegistrationForwardUrl.php         # Event: register:user → override forward URL
│   ├── GenerateRegistrationUrl.php        # Event: registration_url:site → embed invite code
│   ├── EntityMenu.php                     # Event: register:menu:entity → group invite menu item
│   ├── GroupTitleMenu.php                 # Event: register:menu:title → remove group invite item
│   ├── PageMenu.php                       # Event: register:menu:page → page menu items
│   ├── RegistrationMiddleware.php         # Route middleware: validate invite code
│   ├── ConfirmGroupInvite.php             # Route controller: confirm group invitation
│   ├── ConfirmInviteAction.php            # Admin action: approve invite request
│   ├── InviteGroupMembersAction.php       # Action: invite users to a group
│   ├── InviteUsersAction.php              # Action: invite users to the site
│   └── RequestInviteAction.php            # Public action: request an invitation
├── lib/
│   └── functions.php                      # Legacy helper functions (used in views/plugins)
├── views/
│   └── default/
│       ├── forms/                         # Invite forms (friends/invite, groups/invite)
│       ├── object/user_invite/            # Invite object views
│       ├── object/user_invite_request/    # Invite request views
│       ├── plugins/hypeInvite/            # Plugin settings view
│       └── resources/                     # Page resources (friends/invite, groups/invite)
├── languages/                             # Translation files
├── docker/                                # Per-plugin Elgg 5.x Docker stack
├── elgg-plugin.php                        # Declarative plugin config
├── elgg-services.php                      # DI service registration (users.invites)
└── composer.json                          # php >=8.2, elgg/elgg ^5.0
```

## Events Registered

All registered via `'events'` key in `elgg-plugin.php`:

| Event | Type | Handler | Purpose |
|-------|------|---------|---------|
| `registration_url` | `site` | `GenerateRegistrationUrl` | Embed invite code in registration URL |
| `register` | `user` | `RegistrationForwardUrl` | Redirect to context (group/friend) after registration |
| `register` | `menu:page` | `PageMenu` | Add invite/admin menu items |
| `register` | `menu:entity` | `EntityMenu` | Add group invite menu item |
| `accept` | `invite` | `AcceptFriendInvitesOnRegistration` | Create friend relationships |
| `accept` | `invite` | `AcceptGroupInvitesOnRegistration` | Join groups on invite acceptance |
| `create` | `user` | `ProcessUserInvitesOnRegistration` | Process pending invites on new user creation |

Plus, via `Bootstrap::init()` when hypeHero is active:
- `register:menu:title` → `GroupTitleMenu` (removes redundant group invite item)
- `register:menu:actions` → `GroupTitleMenu`

## Routes

| Name | Path | Handler | Access |
|------|------|---------|--------|
| `account:register` | `/register` | `account/register` resource | Public (walled: false), `RegistrationMiddleware` |
| `friends:invite` | `/friends/{username?}/invite` | `friends/invite` resource | Auth + `UserPageOwnerGatekeeper` |
| `invite:request` | `/users/request_invitation` | resource | Public (walled: false) |
| `invite:group:group:confirm` | `/groups/confirm_invitation` | `ConfirmGroupInvite` | Public (walled: false) |

## Actions

| Action | Controller | Access |
|--------|-----------|--------|
| `users/request_invitation` | `RequestInviteAction` | Public |
| `users/confirm_invitation` | `ConfirmInviteAction` | Admin |
| `users/invite` | `InviteUsersAction` | Logged-in |
| `groups/invite` | `InviteGroupMembersAction` | Logged-in |

## Group Tools

- `invites` — default off; when enabled shows invite menu in group entity menu

## Dependencies

No required plugin dependencies declared. Optional integration:
- `friend_request` — if active, creates `friendrequest` relationships instead of direct friendships
- `groups` — if active, redirects to group invitations page on registration
- `hypeHero` — if active, removes duplicate menu item from hero title/actions menu

## Services (DI)

- `users.invites` → `InviteService` — registered in `elgg-services.php`

## Migration Notes (4.x → 5.x)

- All `'hooks'` declarations merged into `'events'` in `elgg-plugin.php`
- `\Elgg\Hook` replaced with `\Elgg\Event` throughout all handler classes
- `elgg_register_plugin_hook_handler()` → `elgg_register_event_handler()`
- `elgg_trigger_plugin_hook()` → `elgg_trigger_event_results()`
- Added `UserPageOwnerGatekeeper` to `friends:invite` route
- Docker infra upgraded to PHP 8.2 + MySQL 8.0 + Elgg 5.1.x
- Test assertions updated: `_elgg_services()->hooks` → `_elgg_services()->events`
