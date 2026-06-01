import { useParams } from "react-router-dom";

export default function DestinationDetailPage() {
    const { id } = useParams();

    return <h1>Destinasi Detail: {id}</h1>;
}
