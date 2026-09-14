Put your built release APK here, named exactly:
  decomat-stationers.apk

This is what the "Download APK directly" button on the homepage links to
(see includes/functions.php -> APK_DOWNLOAD_URL). Build it with:
  flutter build apk --release
from inside flutter_app/, then upload the resulting file from
build/app/outputs/flutter-apk/app-release.apk here, renamed as above.

Note: APKs installed this way (outside the Play Store) will show Android's
"Unknown sources" warning to installers - that's expected Android
behavior, not a bug in the app. The Play Store download won't show this
warning once your listing is live.
