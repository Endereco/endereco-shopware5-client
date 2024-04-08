#!/bin/bash

command -v git

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "git needs to be installed."
    exit 1 # Exit with error code 1
fi

# Array of version tags to be downloaded
versions=("5.3.0" "5.3.7" "5.4.6" "5.5.10" "5.6.10" "5.7.0") 

# Base repository URL
repo_url="https://github.com/shopware5/shopware.git"

# Destination base directory
base_dir="shops"

# Remove existing directories within shops to start fresh
rm -rf "$base_dir"/*

# Iterate over the versions array
for version in "${versions[@]}"; do
    # Define the destination directory for the current version
    dest_dir="$base_dir/$version"

    # Create the destination directory if it doesn't exist
    mkdir -p "$dest_dir"

    # Attempt to download and extract the archive directly without .git metadata
    if git archive --remote="$repo_url" --format=tar "v$version" | tar -x -C "$dest_dir"; then
        echo "Successfully extracted version $version into $dest_dir without .git metadata"
    else
        echo "Fallback to cloning due to remote archive extraction failure."
        # Clone the specific tag into the destination directory, then remove the .git directory
        if git clone --branch "v$version" --depth 1 "$repo_url" "$dest_dir"; then
            rm -rf "$dest_dir/.git"
            echo "Successfully cloned and cleaned version $version into $dest_dir"
        else
            echo "Failed to clone version $version. Skipping..."
            continue
        fi
    fi

    echo "Successfully cloned version $version into $dest_dir"

    # Navigate into the directory and run Composer install using a Docker container with PHP 7.4
    echo "Running Composer install for version $version..."
    docker run --rm -v "$(pwd)/$dest_dir":/app composer:1.10.1 composer install --no-dev --ignore-platform-reqs

done

echo "All specified versions processed."
