# Populate

## Description

The Populate plugin is a tool designed to populate your WordPress database with dummy data using the WP-CLI command line interface. This can be useful for testing and development purposes, allowing you to quickly generate posts, pages, authors, categories, tags, and more.

## Features

- Generate dummy posts with various options such as tags, categories, authors, comments, and images.
- Create dummy pages with customizable options.
- Generate dummy authors with specified attributes.
- Quickly populate your WordPress database with categories and tags.
- Easy-to-use WP-CLI commands for streamlined data population.

## Usage

The plugin provides WP-CLI commands for populating your WordPress database. Here are some examples:

### Populate Posts
- Populate the WordPress database with dummy posts.
```bash
wp populate post --count=10 --tags=true --category=true --author=true --comment=true --image=true --all=true
```

### Populate Pages
- Populate the WordPress database with dummy pages.
```bash
wp populate page --count=5
```

### Populate Authors
- Populate the WordPress database with dummy authors.
```bash
wp populate author --count=5
```

### Populate Categories
- Populate the WordPress database with dummy categories.
```bash
wp populate category --count=5
```

### Populate Tags
- Populate the WordPress database with dummy tags.
```bash
wp populate tag --count=5
```

### Populate All
- Populate post and pages with all options
```bash
wp populate all
```

## Contributing
Feel free to contribute to this project by submitting issues or pull requests on the this GitHub repository.

