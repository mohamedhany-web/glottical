#!/usr/bin/env bash
# Run ON the LiveKit VPS after exporting keys:
#   export LIVEKIT_API_KEY='...'
#   export LIVEKIT_API_SECRET='...'
#   sudo -E bash scripts/install-livekit-keys-on-vps.sh
set -euo pipefail
API_KEY="${LIVEKIT_API_KEY:-}"
API_SECRET="${LIVEKIT_API_SECRET:-}"
if [[ -z "$API_KEY" || -z "$API_SECRET" ]]; then
  echo "Set LIVEKIT_API_KEY and LIVEKIT_API_SECRET first."
  exit 1
fi
CFG=""
for f in /etc/livekit.yaml /opt/livekit/livekit.yaml /root/livekit.yaml /etc/livekit/config.yaml; do
  [[ -f "$f" ]] && CFG="$f" && break
done
[[ -n "$CFG" ]] || { echo "livekit.yaml not found"; exit 1; }
cp -a "$CFG" "${CFG}.bak.$(date +%s)"
python3 - "$CFG" "$API_KEY" "$API_SECRET" <<'PY'
from pathlib import Path
import re, sys
cfg, key, secret = Path(sys.argv[1]), sys.argv[2], sys.argv[3]
text = cfg.read_text()
block = f"keys:\n  {key}: {secret}\n"
if re.search(r"(?m)^keys:\s*$", text) or re.search(r"(?m)^keys:\s*\n", text):
    text = re.sub(r"(?ms)^keys:\n(?:[ \t].*\n)*", block, text, count=1)
else:
    text = text.rstrip() + "\n\n" + block
cfg.write_text(text)
print(f"Updated {cfg} with key {key}")
PY
if systemctl list-unit-files 2>/dev/null | grep -qi '^livekit'; then
  systemctl restart livekit 2>/dev/null || systemctl restart livekit-server 2>/dev/null || true
fi
curl -fsS http://127.0.0.1:7880/ | head -c 40; echo
echo "LiveKit keys installed."
