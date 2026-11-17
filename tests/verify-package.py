"""Check an installable ZIP against a Git tree, commit or tag, byte for byte."""

import subprocess
import sys
import zipfile

archive, ref, version = sys.argv[1:]
paths = ["assets", "templates", "kingring-support-pages.php", "README.md"]
listing = subprocess.check_output(
    ["git", "ls-tree", "-r", ref, "--", *paths], text=True
)
expected = {}
for line in listing.splitlines():
    metadata, path = line.split("\t", 1)
    expected["kingring-support-pages/" + path] = metadata.split()[2]

with zipfile.ZipFile(archive) as package:
    assert package.testzip() is None, "ZIP integrity failed"
    entries = [entry.filename for entry in package.infolist() if not entry.is_dir()]
    assert len(entries) == len(set(entries)), "Duplicate ZIP entries"
    assert set(entries) == set(expected), "ZIP paths differ from the source tree"
    for path, sha in expected.items():
        actual = subprocess.check_output(
            ["git", "hash-object", "--stdin"], input=package.read(path)
        ).decode().strip()
        assert actual == sha, f"Content differs: {path}"
    source = package.read("kingring-support-pages/kingring-support-pages.php").decode()
    assert "Plugin Name: Codex Plugin" in source
    assert f"Version: {version}" in source
    assert f"'KINGRING_SUPPORT_PAGES_VERSION', '{version}'" in source
    assert "'auto_update_plugin'" in source
    assert "str_replace( '{year}', '2027'" in source
    readme = package.read("kingring-support-pages/README.md").decode()
    assert f"Current version: **{version}**" in readme

print(f"PASS: {len(expected)} package files match {ref}, version {version}.")
