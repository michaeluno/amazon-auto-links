# Guidelines for Developers
For those who contribute to the project should follow these guidelines.

## Commit Message Format
Each commit message consists of a header, a body and a footer. The header has a special format that includes a tag and a subject:

```
<label> <subject> <tag>
<BLANK LINE>
<body>
<BLANK LINE>
<footer>
```

The header is mandatory and the label, tag of the header is optional.

### Subject
The subject contains succinct description of the change. Think it as a title of an online forum topic.

- use the imperative, present tense: "change" not "changed" nor "changes".
- do not add a period at the end.

#### Labels  
A colon (`:`) follows after a label.

Mostly used tags are:  

- `Feat:` or `Feature:` - A new feature.
- `Fix:` - A bug fix.
- `Docs:` or `Document:` - Documentation only changes.
- `Style:` - Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc).
- `Refactor:` - A code change that neither fixes a bug nor adds a feature.
- `Optimize:` - A code change that improves performance.
- `Tweak:` - A code change that make slight changes to program behaviors.
- `Test:` - Adding missing or correcting existing tests.
- `Debug:` - Changes regarding debugging.
- `Scratch:` - Adding scratches.
- `Setup:` - Changes to the build process or auxiliary tools and libraries to set up.
- `Chore:` - Changes to the build process or auxiliary tools and libraries such as documentation generation.
- `Release:` - Changes relating to releases

It's not restricted to those and use any terms which will help understand its purpose if necessary.

#### Tags
Optionally, add component names enclosed in a square brackets (`[]`) at the end as a tag. This is for searching commit log.

```
Fix: fix a bug that adds an empty string at the end of the output [Component Name]  
```

### Body
Use the imperative, present tense: "change" not "changed" nor "changes". The body should include the motivation/reason for the change and contrast this with previous behavior.

### Footer
Add remarks. The footer should contain any information about Breaking Changes and is also the place to reference GitHub issues that this commit closes.

Breaking Changes should start with the word `BREAKING CHANGE:` with a space or two newlines. The rest of the commit message is then used for this.

A detailed explanation can be found in this document.