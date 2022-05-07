import apiFetch from '@wordpress/api-fetch';
import useSWR, { Key, SWRConfiguration, SWRResponse } from 'swr';

const fetcher = (path: string) => apiFetch({ path });

export default function useApiFetch<T, E>(key: Key, options?: SWRConfiguration): SWRResponse<T, E> {
	return useSWR(key, fetcher, options);
}
