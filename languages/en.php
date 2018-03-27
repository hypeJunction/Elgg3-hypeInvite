<?php

return [

	'item:object:user_invite' => 'Invitation',
	'collection:object:user_invite' => 'Invitations',
	'item:object:user_invite_request' => 'Invitation Request',
	'collection:object:user_invite_request' => 'Invitation Requests',

	'users:invite' => 'Invite',
	'users:invite:invite' => 'Invite new users',
	'admin:users:invite' => 'Invite New Users',
	'admin:users:invitations' => 'Sent Invitations',
	'admin:users:requests' => 'Invitation Requests',
	'users:invite:emails:select' => 'Emails to invite',
	'users:invite:emails:select:help' => 'Enter one email per line',
	'users:invite:message' => 'Message to include in the invitation',
	'users:invite:resend' => 'Resend invitations to previously invited emails',
	'users:invite:notify:subject' => 'You are invited to join %s',
	'users:invite:notify:body' => '%1$s has invited you to join %2$s.

		%3$s
		Please visit the following link to create an account:
		%4$s

		%6$s
		',
	'users:invite:notify:message' => '
		They have included the following message for you:
		%s

		',

	'users:invite:notify:invite_code' => '
		Please use the following invitation code:
		%1$s
		',

	'users:invite:settings:invite_only_network' => 'Invite Only Registration',
	'users:invite:settings:invite_only_network:help' => 'If enabled, only users with a valid invitation code will be allowed to register',
	'users:invite:settings:request_invitation' => 'Invite Requests',
	'users:invite:settings:request_invitation:help' => 'If enabled, unregistered users will be able to request an invitation to the invite only network',
	'users:invite:settings:invite_code_register_form' => 'Invite Code Field',
	'users:invite:settings:invite_code_register_form:help' => 'If disabled, the invitation code field won\'t be shown on the registration form',
	'users:invite:settings:invitation_codes' => 'Invitation codes',
	'users:invite:settings:invitation_codes:help' => 'Please list site-wide invitation codes (one per line) that can be used by any user to register',
	'users:invite:settings:friends_accept_on_register' => 'Automatically accept off-site friend requests',
	'users:invite:settings:friends_accept_on_register:help' => '
		This plugin keeps tracks of all invites ever sent to the same email address.
		If enabled, this feature will automatically accept all friend requests ever sent to the registering user\'s email address.
		Otherwise, only the request that was clicked on in a single email notification will be accepted.
	',
	'users:invite:settings:invite_friends' => 'Users: Off-Site Invitations',
	'users:invite:settings:invite_friends:help' => 'Allow non-admin users to invite unregistered members to the site',
	'users:invite:settings:invite_groups' => 'Groups: Off-Site Invitations',
	'users:invite:settings:invite_groups:help' => 'Allow group admins (non-admin users) to invite new people to the group',

	'users:invite:result:invited' => '%s of %s invitations were successfully sent',
	'users:invite:result:skipped' => '%s of %s invitations were skipped, because users have already been invited or have an account',
	'users:invite:result:error' => '%s of %s invitations could not be sent due to errors',

	'users:invite:invitation_code' => 'Invitation Code',
	'users:invite:invitation_code:mismatch' => 'The invitation code you have provided is not valid',
	'users:invite:invitation_code:empty' => 'This is an invitation only network. Please request an invitation to register.',

	'groups:invite:settings:require_confirmation' => 'Groups: Require confirmation from invitees',
	'groups:invite:settings:require_confirmation:help' => 'Invited users must always accept invitation. When enabled, this feature will prevent group admins from adding users to the group without invitation',
	'groups:invite:settings:users_tab' => 'Groups: Allow any registered user to be invited',
	'groups:invite:settings:users_tab:help' => 'If enabled, users will be able to find and invite any registered user to a group. If disabled, only friends can be invited',
	'groups:invite:settings:emails_tab' => 'Groups: Allow invitation by email',
	'groups:invite:settings:emails_tab:help' => 'If enabled, users will be able to invite other people to the group via email',
	'groups:invite:settings:groups_accept_on_register' => 'Groups: Automatically accept off-site group invitations',
	'groups:invite:settings:groups_accept_on_register:help' => '
		This plugin keeps tracks of all invites ever sent to the same email address.
		If enabled, this feature will automatically accept all group invitations ever sent to registering user\'s email address.
		Otherwise, only the request that was clicked on in a single email notification will be accepted.
	',

	'groups:invite:friends' => 'Friends',
	'groups:invite:users' => 'Users',
	'groups:invite:emails' => 'Emails',
	'groups:invite:friends:select' => 'Friends to invite',
	'groups:invite:users:select' => 'Users to invite',
	'groups:invite:emails:select' => 'Emails to invite',
	'groups:invite:emails:select:help' => 'Enter one email per line',
	'groups:invite:message' => 'Message to include in the invitation',

	'groups:invite:resend' => 'Resend invitations to previously invited members',
	'groups:invite:action:invite' => 'Send invitation to become a member',
	'groups:invite:action:add' => 'Add as member without invitation',

	'groups:invite' => 'Invite members',
	'groups:invite:title' => 'Invite members to this group',
	'groups:inviteto' => "Invite members to '%s'",

	'groups:tool:invites' => 'Allows members to invite other members',
	'groups:invite:not_found' => 'Group not found',

	'groups:invite:notify:subject' => 'You are invited to join %s',
	'groups:invite:notify:body' => '%1$s invites you to join %2$s at %3$s.

		%4$s
		Please visit the following link to create an account:
		%5$s
		
		%7$s
		',
	'groups:invite:notify:message' => '
		They have included the following message for you:
		%s

		',

	'groups:invite:notify:invite_code' => '
		Please use the following invitation code:
		%1$s
		
		',

	'groups:invite:user:subject' => "%s invites you to join %s",
	'groups:invite:user:body' => "Hi %s,

%s invites you to join '%s'. Click below to confirm the invitation:

%s",

	'groups:invite:result:invited' => '%s of %s invitations were successfully sent',
	'groups:invite:result:skipped' => '%s of %s invitations were skipped, because users have already been invited',
	'groups:invite:result:added' => '%s of %s users were added as group members',
	'groups:invite:result:error' => '%s of %s invitations could not be sent due to errors',

	'groups:invite:confirm:error' => 'Your request can not be completed. Please login and confirm the invitation manually',

	'notification:invite' => 'Notification sent when a non-registered user is invited to join the site',
	'notification:groups_invite_user' => 'Notification sent when a non-registered user is invited to join a group',
	'notification:groups_invite_member' => 'Notification sent when a registered user is invited to join a group',

	'users:invite:request' => 'Request an Invitation',
	'users:invite:invite_only_network:request' => 'This is an invitation-only network. If you do not have an invitation code, please %s',
	'users:invite:request:name' => 'Name',
	'users:invite:request:email' => 'Email Address',
	'users:invite:request:message' => 'Message',
	'users:invite:request:message:help' => 'Please describe briefly why you would like to join our network',
	'users:invite:request:success' => 'Your request has been sent. We will respond shortly',
	'users:invite:request:notify:subject' => 'New invitation request',
	'users:invite:request:notify:message' => '
		%s <%s> requested an invitation.
		%s
		
		You can manage invitations requests here:
		%s
	',
	'users:invite:no_results' => 'No invitations has been sent yet',
	'users:invite:request:no_results' => 'You have not received any invitation requests yet',
	'users:invite:request:confirm_invitation' => 'Accept',
	'users:invite:request:confirm_invitation:success' => 'Invitation has been sent',
	'users:invite:request:confirm_invitation:error' => 'Invitation could not been sent',

	'ViewColumn:view:object/user_invite_request/sender' => 'Sender',
	'ViewColumn:view:object/user_invite_request/message' => 'Message',


	'ViewColumn:view:object/user_invite/invitee' => 'Invitee',
	'ViewColumn:view:object/user_invite/invited_by' => 'Inviter(s)',
	'ViewColumn:view:object/user_invite/invited_to' => 'Group Invite(s)',

];
