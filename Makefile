# WCS4 plugin – Makefile

HOOK_SRC := scripts/pre-commit
HOOK_DST := .git/hooks/pre-commit

.PHONY: install-hook
install-hook:
	@mkdir -p .git/hooks
	cp "$(HOOK_SRC)" "$(HOOK_DST)"
	chmod +x "$(HOOK_DST)"
	@echo "Hook pre-commit zainstalowany w $(HOOK_DST)"
