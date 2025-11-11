#!/bin/bash

command -v git

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "git needs to be installed."
    exit 1 # Exit with error code 1
fi

# Array of version tags to be downloaded
declare -A versions=( ["5.3.0"]="1" ["5.3.7"]="1" ["5.4.6"]="1" ["5.5.10"]="1" ["5.6.10"]="1" ["5.7.19"]="2")

# Base repository URL
repo_url="https://github.com/shopware5/shopware.git"

# Destination base directory
base_dir="shops"

# Check if a version argument is provided
if [ $# -gt 0 ]; then
    selected_version="$1"

    # Check if the provided version exists in the predefined list
    if [[ -z "${versions[$selected_version]}" ]]; then
        echo "Error: Version $selected_version not found in predefined list."
        echo "Available versions: $(printf '%s\n' "${!versions[@]}" | sort -V | tr '\n' ' ' | sed 's/ $//')"
        exit 1
    fi

    # Restrict the loop to only the selected version
    versions=([$selected_version]=${versions[$selected_version]})
fi

# Iterate over the versions array
for version in "${!versions[@]}"; do
    # Define the destination directory for the current version
    dest_dir="$base_dir/$version"

    # Create the destination directory if it doesn't exist
    rm -rf "$dest_dir"
    mkdir -p "$dest_dir"

    # Determine which Composer version to use based on the version
    composer_version="${versions[$version]}"

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
    docker run --rm -v "$(pwd)/$dest_dir":/app composer:$composer_version  composer install --no-dev --ignore-platform-reqs

done

# Change ownership of the copied files to the current user 
sudo chown -R $(whoami):$(whoami) "./shops"

echo "All specified versions processed."
