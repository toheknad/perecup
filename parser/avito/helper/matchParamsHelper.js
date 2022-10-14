module.exports = (config, params) =>
{
    let result = [];
    for (const [key, value] of Object.entries(config)) {
        let currentParam = Object.values(params).find((obj) => {
            return obj.type === value;
        });

        if (currentParam ) {
            result.push({
                key: key,
                value: currentParam.value.replace(value, ''),
                ruName: currentParam.type
            });
        }
    }
    return result;
}