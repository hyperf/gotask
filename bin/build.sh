#!/usr/bin/env bash
package=../example/mongo_client/sidecar.go
package_name=mongo-proxy

#the full list of the platforms: https://golang.org/doc/install/source#environment
platforms=(
"darwin/amd64"
"linux/amd64"
)

for platform in "${platforms[@]}"
do
    platform_split=(${platform//\// })
    GOOS=${platform_split[0]}
    GOARCH=${platform_split[1]}
    output_name=$package_name'-'$GOOS'-'$GOARCH
    if [ $GOOS = "windows" ]; then
        output_name+='.exe'
    fi
    echo GOOS=$GOOS GOARCH=$GOARCH go build -o $output_name $package
    GOOS=$GOOS GOARCH=$GOARCH go build -o $output_name $package
    if [ $? -ne 0 ]; then
        echo 'An error has occurred! Aborting the script execution...'
        exit 1
    fi
done