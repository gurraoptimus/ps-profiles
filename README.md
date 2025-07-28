# PSN Profile Viewer

This project displays a PlayStation Network (PSN) profile summary using PHP. It fetches data from [psnprofiles](https://psnprofiles.com/) and shows the user's avatar, level, and trophy counts.

## Features

- Fetches PSN profile data (username, avatar, level, trophies)
- Displays Platinum, Gold, Silver, and Bronze trophy counts
- Responsive, styled profile card

## Usage

1. Place the PHP file in your web server directory.
2. Access the page with optional query parameters:
    - `psnId` (PSN username, default: `PlayStationUS`)
    - `avatar` (Avatar ID, default: `default-avatar-id`)

Example:
```
http://localhost/ps-profile/?psnId=YourPSNID&avatar=YourAvatarID
```

## Example Output

![Profile Example](https://i.psnprofiles.com/avatars/m/DefaultAvatar_m.png)

| Trophy Type | Count |
|-------------|-------|
| 🏆 Platinum | 0     |
| 🥇 Gold     | 0     |
| 🥈 Silver   | 0     |
| 🥉 Bronze   | 0     |

## Requirements

- PHP 7.0+
- Internet access (to fetch profile data)

## License

This project is provided for educational purposes.