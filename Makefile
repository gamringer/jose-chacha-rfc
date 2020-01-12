FORMATS := html txt
TARGETS := $(foreach ext,$(FORMATS),draft-amringer-jose-chacha-rfc-02.$(ext))

.PHONY: all clean publish
all: $(TARGETS)

publish: all
	mkdir -p pages
	cp $(TARGETS) pages/

clean:
	rm -f $(TARGETS) draft-amringer-jose-chacha-rfc-02.xml
	rm -rf pages

draft-amringer-jose-chacha-rfc-02.xml: jose-chacha.md
	mmark -xml2 -page $< $@

%.txt: %.xml
	xml2rfc --text $<

%.html: %.xml
	xml2rfc --html $<
