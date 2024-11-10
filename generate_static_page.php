<?php
function generateStaticPage($user_id, $username, $email, $profile_image) {
    $static_folder = 'profile/'; // Updated directory for static profile pages
    $file_path = $static_folder . $user_id . '.html'; // Updated path for the static page

    // Generate the HTML content for the profile page
    $html_content = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$username's Profile</title>
        <link rel='stylesheet' href='../styles.css'>
    </head>
    <body>
        <header>
            <h1>$username's Profile</h1>
        </header>
        <section>
            <img src='../$profile_image' alt='$username image' style='width: 200px; height: 200px;'>
            <p><strong>Username:</strong> $username</p>
            <p><strong>Email:</strong> $email</p>
        </section>
    </body>
    </html>";

    // Write the HTML content to the file
    file_put_contents($file_path, $html_content);
}
?>
