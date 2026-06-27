PHP_VERSION ?= 8.3
PHP_VERSIONS := 8.3 8.4 8.5
IMAGE_PREFIX := turahe/laravel-likeable-test
IMAGE := $(IMAGE_PREFIX):$(PHP_VERSION)
DOCKER_RUN := docker run --rm -v "$(CURDIR):/app" -w /app $(IMAGE)

define version_targets
build-$(1):
	docker build --build-arg PHP_VERSION=$(1) -t $(IMAGE_PREFIX):$(1) .

test-$(1): build-$(1)
	docker run --rm -v "$(CURDIR):/app" -w /app $(IMAGE_PREFIX):$(1)

clean-image-$(1):
	-docker rmi $(IMAGE_PREFIX):$(1)

endef

$(foreach v,$(PHP_VERSIONS),$(eval $(call version_targets,$(v))))

.PHONY: build build-all test test-all install shell format clean clean-all
.PHONY: $(addprefix build-,$(PHP_VERSIONS)) $(addprefix test-,$(PHP_VERSIONS)) $(addprefix clean-image-,$(PHP_VERSIONS))

build-all: $(addprefix build-,$(PHP_VERSIONS))

build: build-$(PHP_VERSION)

test-all: $(addprefix test-,$(PHP_VERSIONS))

test: test-$(PHP_VERSION)

install: build
	$(DOCKER_RUN) composer-install.sh

shell: build
	docker run --rm -it -v "$(CURDIR):/app" -w /app $(IMAGE) bash

format: build
	$(DOCKER_RUN) sh -c "composer-install.sh && composer format"

clean-all: $(addprefix clean-image-,$(PHP_VERSIONS))

clean:
	-docker rmi $(IMAGE)
