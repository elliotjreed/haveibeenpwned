#!/bin/bash

current_directory=$(pwd)
current_directory=$(basename "$current_directory")

git_name=$(git config user.name);
git_email=$(git config user.email);

read -p "Author name ($git_name): " author_name
author_name=${author_name:-$git_name}

read -p "Author email ($git_email): " author_email
author_email=${author_email:-$git_email}


username_guess=${author_name//+([^[:alnum:][:blank:]])/_,,}
read -p "Author GitHub / Packagist username ($username_guess): " username
username=${username:-elliotjreed}

read -p "Author website or profile (https://github.com/$username): " author_website
default_website="https://github.com/$username"
author_website=${author_website:-$default_website}

read -p "Package name ($current_directory): " package_name
package_name=${package_name:-$current_directory}

read -p "Package description: " package_description
package_description=${package_description:-PHP}

read -p "Top-level namespace (ElliotJReed): " namespace
namespace=${namespace:-ElliotJReed}

echo
echo "This script will replace the above values in all files in the project directory and reset the git repository."
read -p "Are you sure you wish to continue? (y/N) " -n 1 -r

if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    [[ "$0" = "$BASH_SOURCE" ]] && exit 1 || return 1
fi

echo

# Only remove .git directory if the template repository was cloned, rather than used as a template via GitHub's "Use this template"
if [[ $(basename "$(git rev-parse --show-toplevel)") = "php-package-template" ]]
then
    rm -rf .git
fi

sed -i '' '/composer.lock/d' .gitignore &>/dev/null

# The `sed` replacements here are just escaping characters for the regex. from the variables defined by `read` above. I've redirected the warnings so as to not freak people out.
find . -type f -exec sed -i '' -e "s/:username/$(echo "$username" | sed -e 's/[]\/$*.^[]/\\&/g')/g" {} \; &>/dev/null
find . -type f -exec sed -i '' -e "s/:author_name/$(echo "$author_name" | sed -e 's/[]\/$*.^[]/\\&/g')/g" {} \; &>/dev/null
find . -type f -exec sed -i '' -e "s/:author_email/$(echo "$author_email" | sed -e 's/[]\/$*.^[]/\\&/g')/g" {} \; &>/dev/null
find . -type f -exec sed -i '' -e "s/:author_website/$(echo "$author_website" | sed -e 's/[]\/$*.^[]/\\&/g')/g" {} \; &>/dev/null
find . -type f -exec sed -i '' -e "s/:package_name/$(echo "$package_name" | sed -e 's/[]\/$*.^[]/\\&/g')/g" {} \; &>/dev/null
find . -type f -exec sed -i '' -e "s/:package_description/$(echo "$package_description" | sed -e 's/[]\/$*.^[]/\\&/g')/g" {} \; &>/dev/null
find . -type f -exec sed -i '' -e "s/:namespace/$(echo "$namespace" | sed -e 's/[]\/$*.^[]/\\&/g')/g" {} \; &>/dev/null

mv tests/ElliotJReed tests/"$namespace"
mv src/ElliotJReed src/"$namespace"

sed -i '' -e '3,12d' README.md &>/dev/null

echo "Replaced all values and removed git directory. This script is self-destructing."

rm demo.gif
rm -- "$0"

echo "Installing dependencies"

composer install --ignore-platform-reqs
